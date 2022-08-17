<?php require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetPageProperty("description", "");
$APPLICATION->SetTitle("Восстановление пароля");
CModule::IncludeModule('iblock');
use Bitrix\Main\UserTable;

$user = UserTable::getList([
    'select' => ['ID'],
    'filter' => ['LOGIN' => $_POST['LOGIN']]
])->fetch();

$userData = CUser::GetByID($user['ID']);
$userAr = $userData->Fetch();
?>

    <style>
        label {
            margin-bottom: 10px;
        }
    </style>

    <section class="section section_reg">
        <div class="container">
            <h1 class="section__title">Восстановление пароля</h1>

                <div class="reg_form__inner">


                    <div class="alert alert-warning" style=" display: none; max-width: 800px; margin: auto; color: red; text-align: center; border: red 1px solid; border-radius: 2rem; margin-bottom: 20px; font-size: 16px; padding-bottom: 10px; padding-top: 10px">
                        <p><font class="errortext"></font></p></div>
                    <form class="reg_form  changepassword-ajax">
                        <label class="with_border">
                            <span>Ваш логин</span>
                            <input name="LOGIN" type="text" value="<?=$_GET['USER_LOGIN']?>">
                        </label>

                        <label class="with_border">
                            <span>Код восстановления</span>
                            <input name="CODE" type="text" value="<?=$_GET['USER_CHECKWORD']?>">
                        </label>

                        <label class="with_border">
                            <span>Новый пароль</span>
                            <input name="PASSWORD" type="password" value="">
                        </label>

                        <label class="with_border">
                            <span>Подтверждение пароля</span>
                            <input name="PASSWORD_CONFIRM" type="password" value="">
                        </label>

                        <div class="authorization-btns">
                            <button type="submit" class="authorization-btn page-recovery-btn btn">Восстановить пароль</button>
                        </div>
                    </form>


        </div>
    </section>

<?php require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>