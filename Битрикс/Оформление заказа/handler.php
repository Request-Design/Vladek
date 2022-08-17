<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("description", "");
if(!CUser::IsAuthorized()) header("Location: /");

CModule::IncludeModule('iblock');
CModule::IncludeModule('sale');



$rsUsers = CUser::GetList(($by="ID"), ($order="desc"));
$login = $rsUsers->Fetch()['ID']+1;
$pass = md5('password'.rand());
$userLogin = 'user'.$login;

$idUser = $USER->GetID();
$rsUser = CUser::GetByID($idUser);
$arUser = $rsUser->Fetch();

$res = \Bitrix\Sale\Location\LocationTable::getList(array(
    'filter' => array(
        '=ID' => $arUser['UF_LOCATION_ID'],
        '=PARENTS.NAME.LANGUAGE_ID' => 'ru',
        '=PARENTS.TYPE.NAME.LANGUAGE_ID' => 'ru',
    ),
    'select' => array(
        'NAME_RU' => 'PARENTS.NAME.NAME',
        'TYPE_CODE' => 'PARENTS.TYPE.CODE',
        'ID' => 'PARENTS.ID',
        'TYPE_NAME_RU' => 'PARENTS.TYPE.NAME.NAME'
    ),
    'order' => array(
        'PARENTS.DEPTH_LEVEL' => 'asc'
    )
));
while($item = $res->fetch())
{
    $location[$item['TYPE_CODE']] = $item['NAME_RU'];
}



function getPropertyByCode($propertyCollection, $code)
{
    foreach ($propertyCollection as $property)
    {
        if($property->getField('CODE') == $code)
            return $property;
    }
}

$data = [
    'DELIVERY'         => filter_var(trim(htmlspecialchars(strip_tags($_POST['order']['DELIVERY']))), FILTER_SANITIZE_STRING),
    'PAYMENT'          => filter_var(trim(htmlspecialchars(strip_tags($_POST['order']['PAYMENT']))), FILTER_SANITIZE_STRING),
    'PERSONAL_PHONE'   => filter_var(trim(htmlspecialchars(strip_tags($_POST['order']['PHONE']))), FILTER_SANITIZE_STRING),
    'NAME'             => filter_var(trim(htmlspecialchars(strip_tags($_POST['order']['NAME']))), FILTER_SANITIZE_STRING),
    'PHONE_NUMBER'     => filter_var(trim(htmlspecialchars(strip_tags($_POST['order']['PHONE_NUMBER']))), FILTER_SANITIZE_STRING),
];
$resz = [];
$deliveryID = $data['DELIVERY'];
$resz['EMAIL'] = $arUser['EMAIL'];
foreach ($data as $key => $val) //Валидация данных
{
    if($val !== '')
    {

        if ($key === 'NAME')
        {
            if(strlen($val) < 256 && strlen($val) > 2) $resz[$key] = $val;
            else
            {
                $_SESSION['DISPLAY'] = 'block';
                $_SESSION['ERRORS']['MESSAGE_ORDER'] = 'Проверьте поле "Ф.И.О. получателя", оно должно быть не менее 2 символов.';
                header('Location: /personal/oformlenie/');die;
            }
        }

        elseif ($key === 'PHONE_NUMBER')
        {
            if(strlen($val) < 13 && strlen($val) > 9) $resz[$key] = $val;
            else
            {
                $_SESSION['DISPLAY'] = 'block';
                $_SESSION['ERRORS']['MESSAGE_ORDER'] = 'Проверьте поле "Номер телефона", оно должно начинаться с 8 и быть не длиннее 11 символов.';
                header('Location: /personal/oformlenie/');die;
            }
        }

        elseif ($key === 'CITY')
        {
            if($deliveryID == 2) continue;
            if (strlen($val) >= 1) $resz[$key] = $val;
            else
            {
                $_SESSION['DISPLAY'] = 'block';
                $_SESSION['ERRORS']['MESSAGE_ORDER'] = 'Вы не указали свой город в личном кабинете.';
                header('Location: /personal/oformlenie/');die;
            }
        }



    }
}

$resz['LOCATION'] = $arUser['UF_LOCATION_ID'];

$idUser = $USER->GetID();
$rsUser = CUser::GetByID($idUser);
$arUser = $rsUser->Fetch();

foreach ($USER->GetUserGroupArray() as $key => $val)
{
    if($val == 10) $userType = 1;
    elseif($val == 11) $userType = 2;
}


$paymentID = $data['PAYMENT'];

$dbBasketItems = CSaleBasket::GetList(
    array(
        "NAME" => "ASC",
        "ID" => "ASC"
    ),
    array(
        "FUSER_ID" => CSaleBasket::GetBasketUserID(),
        "LID" => SITE_ID,
        "ORDER_ID" => "NULL",
    ),
    false,
    false,
    array('PRODUCT_ID', 'NAME', 'PRICE', 'CURRENCY', 'QUANTITY', 'PRICE_TYPE_ID', 'BASE_PRICE', 'WEIGHT')
);
while ($arItems = $dbBasketItems->Fetch()) //Получаем данные по заказам
{
    $products[] = $arItems;
    $sum += $arItems['PRICE'];
    $quantity += $arItems['QUANTITY'];

    $counts = CIBlockElement::GetList(array(), Array('IBLOCK_ID' => array(2,3), 'ID' => $arItems['PRODUCT_ID'], 'ACTIVE' => 'Y'), false,
        array(), array('PROPERTY_COUNT_SALES'));
    $count = $counts->GetNext();

    CIBlockElement::SetPropertyValuesEx($arItems['PRODUCT_ID'], false,
        array('COUNT_SALES' => $count['PROPERTY_COUNT_SALES_VALUE'] + $arItems['QUANTITY']));
}

if ($_POST['order'] !== null):

    $basket = Bitrix\Sale\Basket::create('s1'); //Создаем экземпляр корзнины

    foreach ($products as $product)
    {
        $item = $basket->createItem("catalog", $product["PRODUCT_ID"]);
        unset($product["PRODUCT_ID"]);
        $item->setFields($product);

    }
    $order = Bitrix\Sale\Order::create('s1', $arUser['ID']);//Создаем экземпляр заказа
    $order->setPersonTypeId($userType);
    $order->setBasket($basket);
    $order->setField('USER_DESCRIPTION', $_POST['order']['DELIVERY_MESSAGE']);

    $shipmentCollection = $order->getShipmentCollection();
    $shipment = $shipmentCollection->createItem(
        Bitrix\Sale\Delivery\Services\Manager::getObjectById($deliveryID)
    );

    $shipmentItemCollection = $shipment->getShipmentItemCollection();


    foreach ($basket as $basketItem)
    {
        $item = $shipmentItemCollection->createItem($basketItem);
        $item->setQuantity($basketItem->getQuantity());
    }

    $paymentCollection = $order->getPaymentCollection();
    $payment = $paymentCollection->createItem(
        Bitrix\Sale\PaySystem\Manager::getObjectById($paymentID)
    );

    $payment->setField("SUM", $order->getPrice());
    $payment->setField("CURRENCY", $order->getCurrency());

    $fullPrice = ceil($order->getPrice());

    $propertyCollection = $order->getPropertyCollection();

    $emailProperty = getPropertyByCode($propertyCollection, 'EMAIL');
    $emailProperty->setValue($resz['EMAIL']);



    if ($userType == 1) //Физ. лицо
    {
        $fioProperty = getPropertyByCode($propertyCollection, 'FIO');
        $fioProperty->setValue($resz['NAME']);

        $phoneProperty = getPropertyByCode($propertyCollection, 'PHONE');
        $phoneProperty->setValue($resz['PHONE_NUMBER']);
    }
    elseif($userType == 2) //Юр. лицо
    {

        $personProperty = getPropertyByCode($propertyCollection, 'CONTACT_PERSON');
        $personProperty->setValue($resz['NAME']);

        $phoneProperty = getPropertyByCode($propertyCollection, 'PHONE');
        $phoneProperty->setValue($resz['PHONE_NUMBER']);

        $innProperty = getPropertyByCode($propertyCollection, 'INN');
        $innProperty->setValue($arUser['UF_INN']);

        $kppProperty = getPropertyByCode($propertyCollection, 'KPP');
        $kppProperty->setValue($arUser['UF_KPP']);

        $companyProperty = getPropertyByCode($propertyCollection, 'COMPANY');
        $companyProperty->setValue($arUser['WORK_COMPANY']);
    }


    //MAIN PROPS

    //DELIVERY PROPS

    $cityProperty = getPropertyByCode($propertyCollection, 'CITY');
    $cityProperty->setValue($location['CITY']);

    //Получаем ID местоположения для заполнения
    $arLocs = \Bitrix\Sale\Location\LocationTable::getById($arUser['UF_LOCATION_ID'])->fetch();

    $userPosition = $arLocs['CODE'];
    $propertyValue = $propertyCollection->getDeliveryLocation(); //Устанавливаем местоположение
    $propertyValue->setValue($userPosition);

    $deliveryDateProperty = getPropertyByCode($propertyCollection, 'DELIVERY_DATE');
    $deliveryDateProperty->setValue('Рассчитываем...');

    $addressProperty = getPropertyByCode($propertyCollection, 'FLAT');
    $addressProperty->setValue($arUser['UF_FLAT']);
    $addressProperty = getPropertyByCode($propertyCollection, 'HOUSE');
    $addressProperty->setValue($arUser['UF_HOUSE']);

    $addressString = $location['STREET'].' д. '.$arUser['UF_HOUSE'];

    if ($arUser['UF_FLAT']) $addressString .= ', кв. '.$arUser['UF_FLAT'];
    $addressProperty = getPropertyByCode($propertyCollection, 'ADDRESS');
    $addressProperty->setValue($addressString);
    if($userType == 2)
    {
        $addressProperty = getPropertyByCode($propertyCollection, 'COMPANY_ADR');
        $addressProperty->setValue($addressString);
    }
    if($deliveryID == 2) $addressProperty->setValue('Самовызов');
    //DELIVERY PROPS

    $result = $order->save();

    if (!$result->isSuccess()): $result->getErrors();
    else:

        $result = array();
        $arFilter = array(
            "USER_ID" => $USER->GetID(),
            "ID",
        );
        $db_sales = CSaleOrder::GetList(array("DATE_INSERT" => "ASC"), $arFilter); //Получаем ID текущего заказа
        while ($sale = $db_sales->Fetch())
        {
            $orderID = $sale['ID'];
        }

        if($paymentID == 11):?>
            <form class="send-payment-ajax" method='POST' action='https://gemmarussia.server.paykeeper.ru/create/' accept-charset="utf-8">

                <input type='hidden' name='sum' value='<?=$fullPrice?>'/> <br />

                <input type='hidden' name='clientid' value='<?=$resz['NAME']?>'/> <br />

                <input type='hidden' name='orderid' value='<?=$orderID?>'/> <br />

                <input type='hidden' name='service_name' value='Тестовая оплата'/> <br />

                <input type='hidden' name='client_phone' value='<?=$resz['PHONE_NUMBER']?>'/> <br />

                <input type='hidden' name='client_email' value='<?=$resz['EMAIL']?>'/> <br />

            </form>


            <script>
                $( document ).ready(function(){
                    $('.send-payment-ajax').submit()
                })
            </script>
        <?php else: header('Location: /personal/moi-zakazi/'.$orderID);?>
        <?php endif;?>

    <?php endif;?>

<?php endif;?>