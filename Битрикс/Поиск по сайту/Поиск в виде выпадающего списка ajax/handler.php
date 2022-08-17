<?php require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');
CModule::IncludeModule('iblock');

$idUser = $USER->GetID();
$rsUser = CUser::GetByID($idUser);
$arUser = $rsUser->Fetch();

$items = CIBlockElement::GetList(array(), Array('IBLOCK_ID' => array(2, 3), 'ACTIVE' => 'Y',
    array(
        "LOGIC" => "OR",
        array('PROPERTY_ARTNUMBER' => '%' . $_POST['INPUT'] . '%', '>QUANTITY' => 0,),
        array('NAME' => '%' . $_POST['INPUT'] . '%', '>QUANTITY' => 0,),
    ),
), false, array('nTopCount' => 5),
    array('ID', 'NAME', 'DETAIL_PAGE_URL', 'QUANTITY', 'IBLOCK_ID'));
while($item = $items->GetNext()):?>
    <a href="<?=$item['DETAIL_PAGE_URL']?><?php if($item['IBLOCK_ID'] == 3) echo '?search='.$item['ID']?>" class="global-search-content-ajax"><li><?=$item['NAME']?></li></a>
<?php endwhile;
?>

