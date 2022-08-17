<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/bitrix/modules/main/include/prolog_before.php");

global $APPLICATION;
$USER = new CUser;

$login = filter_var(trim(htmlspecialchars(strip_tags($_POST['LOGIN']))), FILTER_SANITIZE_STRING);
$pass  = filter_var(trim(htmlspecialchars(strip_tags($_POST['PASSWORD']))), FILTER_SANITIZE_STRING);

if($login !== '' && $pass !== '')
{
    $arAuthResult = $USER->Login($login, $pass, "Y");
    $result = $APPLICATION->arAuthResult = $arAuthResult;

    if(strval($result) == 1) print_r(1);
    else die('Неверный логин или пароль.');
}
else
{
    $result = 'Вы не указали все данные.';
}
