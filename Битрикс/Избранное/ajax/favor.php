<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/bitrix/modules/main/include/prolog_before.php");
global $APPLICATION;

if($_POST['favor'])
{
    if($USER->IsAuthorized()) // Для авторизованного
    {
        $idUser = $USER->GetID();
        $rsUser = CUser::GetByID($idUser);
        $arUser = $rsUser->Fetch();
        $arElements = $arUser['UF_FAVOURITES'];  // Достаём избранное пользователя
        if(!in_array($_POST['favor'], $arElements)) // Если еще нету этой позиции в избранном
        {
            $arElements[$_POST['favor']] = $_POST['favor']; $result = 1;
        }
        else
        {
            $key = array_search($_POST['favor'], $arElements); // Находим элемент, который нужно удалить из избранного
            unset($arElements[$key]); $result = 2;
        }
        $USER->Update($idUser, Array("UF_FAVOURITES" => $arElements)); // Добавляем элемент в избранное
    }
    else // Для неавторизованного
    {
        $arElements = unserialize($_COOKIE['favorites']);

        if( $_SESSION['favourites'][$_POST['favor']] )
        {
            unset($_SESSION['favourites'][$_POST['favor']]); $result = 2;
        }
        else
        {
            $_SESSION['favourites'][$_POST['favor']] = $_POST['favor']; $result = 1;
        }
    }
}
die($result);
?>