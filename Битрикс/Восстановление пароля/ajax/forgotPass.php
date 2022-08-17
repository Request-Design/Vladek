<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/bitrix/modules/main/include/prolog_before.php");
global $APPLICATION;

use Bitrix\Main\UserTable;

$email = filter_var(trim(htmlspecialchars(strip_tags($_POST['EMAIL']))), FILTER_SANITIZE_STRING);

$user = UserTable::getList([
    'select' => ['ID', 'LOGIN'],
    'filter' => ['EMAIL' => $_POST['EMAIL']]
])->fetch();

if(!$user) die('Аккаунт с данной почтой не существует.');

$key = md5(rand());
$message = "Информационное сообщение сайта EXAMPLE
------------------------------------------
".$user['LOGIN'].",

Для смены пароля перейдите по следующей ссылке:
https://".$_SERVER['SERVER_NAME']."/change_pass_proccesing/index.php?change_password=yes&lang=ru&USER_CHECKWORD=".$key."&USER_LOGIN=".$user['LOGIN']."

Ваша регистрационная информация:

Login: ".$user['LOGIN']."
Код восстановления: ".$key."

Сообщение сгенерировано автоматически.";
$header = "From: sale@example.ru";
mail($email, 'Восстановление пароля', $message, $header);

$USER->Update($user['ID'], array('UF_RECOVERY_CODE' => $key));
die(1);?>