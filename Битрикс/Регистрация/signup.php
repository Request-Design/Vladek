<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/bitrix/modules/main/include/prolog_before.php");
use Bitrix\Main\UserTable;
global $APPLICATION;
$USER = new CUser;

$idUser = CUser::GetID();
$rsUser = CUser::GetByID($idUser);
$userAr = $rsUser->Fetch();

$data = [
    'LOGIN'             => filter_var(trim(htmlspecialchars(strip_tags($_POST['ID']))), FILTER_SANITIZE_STRING),
    'FCS'               => filter_var(trim(htmlspecialchars(strip_tags($_POST['FCS']))), FILTER_SANITIZE_STRING),
    'EMAIL'             => filter_var(trim(htmlspecialchars(strip_tags($_POST['EMAIL']))), FILTER_SANITIZE_STRING),
    'PERSONAL_BIRTHDAY' => filter_var(trim(htmlspecialchars(strip_tags($_POST['BIRTHDAY']))), FILTER_SANITIZE_STRING),
    'PASSWORD'          => filter_var(trim(htmlspecialchars(strip_tags($_POST['PASSWORD']))), FILTER_SANITIZE_STRING),
];

$result = Array(
    "LID"               => "ru",
    "ACTIVE"            => "N",
    "GROUP_ID"          => array(6, 3, 10),
);

        foreach ($data as $key => $val)
        {
            if($key == 'LOGIN')
            {
                if (strlen($val) == 10)
                {
                    $n = 0;
                    $letter = $data['LOGIN'];
                    $idNums = substr($val, 2, 8);

                    if(substr($val, 0, 2) !== 'RU' || !is_numeric($idNums)) die('Ошибка при заполнении ID. Укажите по следующему шаблону: RU00000000');

                    $check = UserTable::getList([
                        'select' => ['ID'],
                        'filter' => ['EMAIL' => $val]
                    ])->fetch();

                    if($check)
                    {
                        if($check['ID'] !== $userAr['ID']) die('Аккаунт с такой почтой уже существует.');
                    }
                    else
                    {
                        $result[$key] = $val;
                        $result['UF_UNIQUE_ID'] = $val;
                    }
                }
                else die('Ошибка при заполнении ID. Укажите по следующему шаблону: RU00000000');
            }
            elseif($key == 'FCS')
            {
                if (strlen($val) >= 5 && strlen($key) < 256)
                {
                    $n = 0;
                    $letter = $data['FCS'];
                    $fcs = explode(' ', $letter);

                    if(count($fcs) < 2) die('В поле "ФИО" должны быть как минимум Фамилия и Имя');
                    else
                    {
                        $result['NAME'] = $fcs[0];
                        $result['LAST_NAME'] = $fcs[1];
                        $result['SECOND_NAME'] = $fcs[2];
                    }
                }
                else die('Проверьте длину поля "ФИО", оно должно быть не меньше 5 и не больше 256 символов.');
            }
            elseif($key == 'PERSONAL_BIRTHDAY')
            {
                if (strlen($val) == 10)
                {
                    $n = 0;
                    $letter = $data['PERSONAL_BIRTHDAY'];
                    $dateBirth = explode('.', $letter);
                    if(count($dateBirth) < 3) die('Ошибка при заполнении даты рождения. Укажите по следующему шаблону: 01.01.1970');
                    else
                    {
                        if(strlen($dateBirth[0]) == 2 && strlen($dateBirth[1]) == 2 && strlen($dateBirth[2]) == 4)
                        {
                            if(date("Y") - $dateBirth[2] < 14 ) die('Вы должны быть старше 14 лет');
                            $result[$key] = $val;
                        }
                        else die('Ошибка при заполнении даты рождения. Укажите по следующему шаблону: 01.01.1970');
                    }
                }
                else die('Ошибка при заполнении даты рождения. Укажите по следующему шаблону: 01.01.1970');
            }
            elseif($key == 'EMAIL' )
            {
                if(strlen($val) > 5 && strlen($val) < 256)
                {
                    $check = UserTable::getList([
                        'select' => ['ID'],
                        'filter' => ['EMAIL' => $val]
                    ])->fetch();

                    if($check)
                    {
                        if($check['ID'] !== $userAr['ID']) die('Аккаунт с такой почтой уже существует.');
                    }
                    else $result['EMAIL'] = $val;
                }
                else die('Проверьте длину поля "E-mail", оно должно быть не больше 255 символов.');

            }
            elseif($key == 'PASSWORD')
            {
                if (strlen($val) > 5) $result[$key] = $val;
                else
                {
                    if ($key === 'PASSWORD' ) die('Длина поля "Пароль" должно быть не менее 5 символов.');
                }
            }

    }

    $USER = new CUser;
    $ID = $USER->Add($result);
    if (intval($ID) > 0) echo 1;
    else die($USER->LAST_ERROR);
