<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");
global $APPLICATION;

$lang = $_POST['LANG'];

$userID = CUser::GetID();
$userData = CUser::GetByID($userID);
$userAr = $userData->Fetch();

$USER = new CUser;

$data = [
    'PAST_PASSWORD' => filter_var(trim(htmlspecialchars(strip_tags($_POST['PAST_PASSWORD']))), FILTER_SANITIZE_STRING),
    'PASSWORD' => filter_var(trim(htmlspecialchars(strip_tags($_POST['PASSWORD']))), FILTER_SANITIZE_STRING),
    'PASSWORD_CONFIRM' => filter_var(trim(htmlspecialchars(strip_tags($_POST['PASSWORD_CONFIRM']))), FILTER_SANITIZE_STRING),
];


if ($lang == 'en') {
    $langAr = [
        'length' => 'The password must be at least 6 characters.',
        'match' => 'Passwords don\'t match',
        'oldForgot' => 'You didn\'t fill the old password.',
        'oldErr' => 'Invalid old password.',
        'newForgot' => 'You forgot to fill a new password.'
    ];
} elseif ($lang == 'ru') {
    $langAr = [
        'length' => 'Пароль должен быть не менее 6 символов.',
        'match' => 'Пароли не совпадают.',
        'oldForgot' => 'Вы не указали старый пароль.',
        'oldErr' => 'Неверный старый пароль.',
        'newForgot' => 'Вы забыли ввести новый пароль.'
    ];
}

if (!$data['PAST_PASSWORD'] || $data['PAST_PASSWORD'] == '' || $data['PAST_PASSWORD'] == null) die($langAr['oldForgot']);

if (\Bitrix\Main\Security\Password::equals($userAr['PASSWORD'], $data['PAST_PASSWORD']) == false) die($langAr['oldErr']);

if ($data['PASSWORD'] !== '' && $data['PAST_PASSWORD'] !== '')
{
    if ($data['PASSWORD'] == $data['PASSWORD_CONFIRM'])
    {
        if (strlen($data['PASSWORD']) >= 6 && strlen($data['PASSWORD']) < 256)
        {
            $result = ['PASSWORD' => $data['PASSWORD'], 'PASSWORD_CONFIRM' => $data['PASSWORD_CONFIRM']];
        } else die($langAr['length']);

    } else die($langAr['match']);

} else die($langAr['newForgot']);


$USER->Update($userID, $result);
print_r(1);







