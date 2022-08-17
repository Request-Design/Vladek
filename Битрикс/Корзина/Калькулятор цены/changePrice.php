<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
Bitrix\Main\Loader::includeModule("iblock");
if ($_POST['count'] <= 0) $_POST['count'] = 1;
$prices = CIBlockElement::GetList(array(), array('IBLOCK_ID' => array(6, 7), 'ID' => $_POST['id'], 'ACTIVE' => 'Y'), false, array(),
    array('ID', 'CATALOG_GROUP_1'));
$element = $prices->GetNext();
$price = $element['CATALOG_PRICE_1'];

if ($_POST['address'] == 'catalog') $result = ceil($price * $_POST['count']);
elseif ($_POST['address'] == 'basket')
{
    Bitrix\Main\Loader::includeModule("sale");
    CSaleBasket::Update($_POST['uid'], array("QUANTITY" => $_POST['count'], "DELAY" => "N"));
    $basket = \Bitrix\Sale\Basket::loadItemsForFUser(\Bitrix\Sale\Fuser::getId(), Bitrix\Main\Context::getCurrent()->getSite());
    $countProduct = array_sum($basket->getQuantityList());
    $priceBasket = ceil($basket->getPrice());

    $result = json_encode(['product' => ceil($price * $_POST['count']), 'basket' => $priceBasket, 'countCart' => $countProduct]);
}

echo $result;
?>