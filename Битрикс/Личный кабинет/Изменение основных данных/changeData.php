<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/bitrix/modules/main/include/prolog_before.php");
use Bitrix\Main\UserTable;
global $APPLICATION;
$USER = new CUser;

$idUser = CUser::GetID();
$rsUser = CUser::GetByID($idUser);
$userAr = $rsUser->Fetch();

$data = [
    'FCS'               => filter_var(trim(htmlspecialchars(strip_tags($_POST['FCS']))), FILTER_SANITIZE_STRING),
    'EMAIL'             => filter_var(trim(htmlspecialchars(strip_tags($_POST['EMAIL']))), FILTER_SANITIZE_STRING),
    'PERSONAL_BIRTHDAY' => filter_var(trim(htmlspecialchars(strip_tags($_POST['BIRTHDAY']))), FILTER_SANITIZE_STRING),
    'PERSONAL_PHONE'    => filter_var(trim(htmlspecialchars(strip_tags($_POST['PHONE']))), FILTER_SANITIZE_STRING),
    'PASSWORD'          => filter_var(trim(htmlspecialchars(strip_tags($_POST['PASSWORD']))), FILTER_SANITIZE_STRING),
    'PASSWORD_CONFIRM'  => filter_var(trim(htmlspecialchars(strip_tags($_POST['PASSWORD_CONFIRM']))), FILTER_SANITIZE_STRING),
    ];




    if ($data['PASSWORD'] == $data['PASSWORD_CONFIRM'])
    {



        if (\Bitrix\Main\Security\Password::equals($userAr['PASSWORD'], $data['PASSWORD_CONFIRM']) == false) die('Неверный старый пароль.');
        else
        {
            foreach ($data as $key => $val)
            {
                    if($key == 'FCS')
                    {
                        if (strlen($val) >= 5 && strlen($key) < 256)
                        {
                            $n = 0;
                            $letter = $data['FCS'];
                            $fcs = [];
                            for ( $i = 0; $i < strlen($letter); $i++ ) //Разбиваем поле ФИО на Ф, И, О
                            {
                                if($letter[$i] !== ' ')  $fcs[$n] .= $letter[$i];
                                else $n++;
                            }
                            if(count($fcs) < 2) die('В поле "ФИО" должны быть как минимум Фамилия и Имя');
                            else
                            {
                                $result['NAME'] = $fcs[0];
                                $result['LAST_NAME'] = $fcs[1];
                                $result['SECOND_NAME'] = ($fcs[2]) ? $fcs[2] : '';
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
                    elseif($key == 'PERSONAL_PHONE')
                    {
                        if(strlen($val) >= 9)
                        {
                            $check = UserTable::getList([
                                'select' => ['ID'],
                                'filter' => ['PERSONAL_PHONE' => $val]
                            ])->fetch();
                            if($check)
                            {
                                if($check['ID'] !== $userAr['ID']) die('Аккаунт с таким номером телефона уже существует.');
                            }
                            else $result['PERSONAL_PHONE'] = $val;
                        }
                        elseif(strlen($val) < 9 && $data[$key]) die('Проверьте свой номер, он должен быть не менее 9 цифр.');
                    }

            }

            $USER = new CUser;
            $idUser = $USER->GetID();
            $USER->Update($idUser, $result);
            echo 1;


        }

    } else die('Пароли не совпадают.');








