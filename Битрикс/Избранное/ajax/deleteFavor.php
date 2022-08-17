<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/bitrix/modules/main/include/prolog_before.php");
global $APPLICATION;

if($_POST['favordelete'])
{
    if($USER->IsAuthorized())
    {
        $idUser = $USER->GetID();
        $rsUser = CUser::GetByID($idUser);
        $arUser = $rsUser->Fetch();
        $arElements = $arUser['UF_FAVOURITES'];
        if(in_array($_POST['favordelete'], $arElements))
        {
            $key = array_search($_POST['favordelete'], $arElements);
            unset($arElements[$key]);
            $result = 1;
        }
        $USER->Update($idUser, Array("UF_FAVOURITES" => $arElements));
    }
    else
    {
        if(in_array($_POST['favordelete'], $_SESSION['favourites']))
        {
            unset($_SESSION['favourites'][$_POST['favordelete']]);
            $result = 1;
        }
    }

}
echo die($result);
?>