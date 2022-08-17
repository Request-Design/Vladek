<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/bitrix/modules/main/include/prolog_before.php");
use Bitrix\Main\UserTable;
global $APPLICATION;

$data = [
    'LOGIN'    => filter_var(trim(htmlspecialchars(strip_tags($_POST['LOGIN']))), FILTER_SANITIZE_STRING),
    'CODE'         => filter_var(trim(htmlspecialchars(strip_tags($_POST['CODE']))), FILTER_SANITIZE_STRING),
    'PASSWORD'         => filter_var(trim(htmlspecialchars(strip_tags($_POST['PASSWORD']))), FILTER_SANITIZE_STRING),
    'PASSWORD_CONFIRM' => filter_var(trim(htmlspecialchars(strip_tags($_POST['PASSWORD_CONFIRM']))), FILTER_SANITIZE_STRING),
];

$user = UserTable::getList([
    'select' => ['ID'],
    'filter' => ['LOGIN' => $_POST['LOGIN']]
])->fetch();

$userData = CUser::GetByID($user['ID']);
$userAr = $userData->Fetch();
$uniqKey = $userAr['UF_RECOVERY_CODE'];

if(!$user) die('Неверный логин.');
if($data['CODE'] !== $uniqKey) die('Неверный код восстановления.');


    if($data['PASSWORD'] !== '' && $data['PAST_PASSWORD'] !== '')
    {
        if($data['PASSWORD'] == $data['PASSWORD_CONFIRM'])
        {
            $result = ['PASSWORD' => $data['PASSWORD'], 'PASSWORD_CONFIRM' => $data['PASSWORD_CONFIRM']];
        }
        else die('Пароли не совпадают.');
    }
    else die('Вы забыли ввести новый пароль.');

$message = "Информационное сообщение сайта EXAMPLE
------------------------------------------
".$user['LOGIN'].",
Ваш пароль был успешно изменён!
https://".$_SERVER['SERVER_NAME']."

Сообщение сгенерировано автоматически.";
$header = "From: sale@example.ru";
mail($email, 'Пароль изменён', $message, $header);

$result['UF_RECOVERY_CODE'] = md5(rand());
$USER->Update($user['ID'], $result);
die(1);