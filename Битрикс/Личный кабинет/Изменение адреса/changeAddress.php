<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/bitrix/modules/main/include/prolog_before.php");
global $APPLICATION;

$data = [
    'UF_LOCATION_ID' => filter_var(trim(htmlspecialchars(strip_tags($_POST['LOCATION']))), FILTER_SANITIZE_STRING),
    'UF_HOUSE' => filter_var(trim(htmlspecialchars(strip_tags($_POST['HOUSE']))), FILTER_SANITIZE_STRING),
    'UF_FLAT' => filter_var(trim(htmlspecialchars(strip_tags($_POST['FLAT']))), FILTER_SANITIZE_STRING),
];

foreach ($data as $key => $val)
{
      if($key == 'UF_LOCATION_ID' )
      {
          if($val && $val > 1) $result[$key] = $val;
          else die('Проверьте поле "Регион и город", там должен быть указан не только регион, но и город');
      }
      elseif($key == 'UF_HOUSE')
      {
          if (!$val) continue;
          if(strlen($val) < 256) $result[$key] = $val;
          else die('Проверьте длину поля "Дом/корпус", оно должна быть не больше 255 символов.');
      }
      elseif($key == 'UF_FLAT')
      {
          if (!$val) continue;
          if(strlen($val) <= 10)
          {
              if($result['UF_HOUSE']) $result[$key] = $val;
              else die('Заполните поле "Дом"');
          }
          else die('Проверьте длину поля "Квартира", оно должна быть не больше 10 символов.');
      }

}
if(!$result['UF_HOUSE'] && !$result['UF_FLAT']) die('Вы забыли указать Дом/корпус или квартиру');

$res = \Bitrix\Sale\Location\LocationTable::getList(array(
    'filter' => array(
        '=ID' => $result['UF_LOCATION_ID'],
        '=PARENTS.NAME.LANGUAGE_ID' => 'ru',
        '=PARENTS.TYPE.NAME.LANGUAGE_ID' => 'ru',
    ),
    'select' => array(
        'NAME_RU' => 'PARENTS.NAME.NAME',
        'TYPE_CODE' => 'PARENTS.TYPE.CODE',
    ),
    'order' => array(
        'PARENTS.DEPTH_LEVEL' => 'asc'
    )
));
while($item = $res->fetch())
{
    $location[$item['TYPE_CODE']] = $item['NAME_RU'];
}

$APPLICATION->IncludeComponent(
    "bitrix:main.include",
    "",
    Array(
        "AREA_FILE_SHOW" => "file",
        "AREA_FILE_SUFFIX" => "inc",
        "COMPOSITE_FRAME_MODE" => "A",
        "COMPOSITE_FRAME_TYPE" => "AUTO",
        "EDIT_TEMPLATE" => "",
        "PATH" => "/api/include.php",
        'CITY_NAME' => $location['CITY']
    )
);
$result['UF_CITY_SDEK_CODE'] = getSdekCities($location['CITY']);
$result['UF_TARIFFS_SDEK_CODE'] = getSdekAvailableRates(44, $result['UF_CITY_SDEK_CODE']);

$USER = new CUser;
$idUser = $USER->GetID();
$USER->Update($idUser, $result);
die(1);