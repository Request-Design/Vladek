<?php require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');
CModule::IncludeModule('iblock');
CModule::IncludeModule('sale');
CModule::IncludeModule('catalog');
$groups = $USER->GetUserGroupArray();
$APPLICATION->IncludeComponent(
    "bitrix:main.include",
    "",
    Array(
        "AREA_FILE_SHOW" => "file",
        "AREA_FILE_SUFFIX" => "inc",
        "COMPOSITE_FRAME_MODE" => "A",
        "COMPOSITE_FRAME_TYPE" => "AUTO",
        "EDIT_TEMPLATE" => "",
        "PATH" => "/include/discount.php"
    )
);

function isSku($iblockID, $elementID)
{
    $mxResult = \CCatalogSku::getOffersList($elementID, $iblockID, array('ACTIVE' => 'Y'), array('ID'));
    if($mxResult) return 1;
    return;
}

$idUser = $USER->GetID();
$rsUser = CUser::GetByID($idUser);
$arUser = $rsUser->Fetch();
$arElements = $arUser['UF_FAVOURITES'];

if ( !empty($arElements) || !empty($_SESSION['favourites']) ):
    if(!(empty($arElements))) $favouritesArr = $arElements;
    else $favouritesArr = $_SESSION['favourites'];

    $favourites = CIBlockElement::GetList(array(), Array('IBLOCK_ID' => 2, 'ACTIVE' => 'Y', 'ID' => $favouritesArr, 'NAME' => '%' . $_POST['INPUT'] . '%'), false, array(),
        array('ID', 'NAME', 'CODE', 'CATALOG_GROUP_1', 'IBLOCK_SECTION_ID', 'PREVIEW_PICTURE', 'QUANTITY', 'DETAIL_PAGE_URL'));
    while($favour = $favourites->GetNext()):?>
    <?php

        $mxResult = \CCatalogSku::getOffersList($favour['ID'], 2, array('ACTIVE' => 'Y', '>QUANTITY' => 0), array('ID'));
        if($mxResult) $quant = 'true';
        else
        {
            if($favour['QUANTITY'] == 0) $quant = 'false';
        }
        if($_POST['AVAILABLE'] == 1 && $quant == 'false') continue;
        ?>

            <article class="product product-favour product-<?=$favour['ID']?> <?php if($quant == 'false') echo 'product--not'?>" >
            <div class="product__image">
                <a href="<?=$favour['DETAIL_PAGE_URL']?>">
                    <img src="<?=CFile::GetPath($favour['PREVIEW_PICTURE'])?>" alt="<?=$favour['NAME']?>">
                </a>
                <button class="product__heart">
                    <img src="<?=SITE_TEMPLATE_PATH?>/img/icon/heart.svg" alt="heart">
                </button>
                <button class="product__close remove" data-favor="<?=$favour['ID']?>">>
                    <img src="<?=SITE_TEMPLATE_PATH?>/img/whitexxx.svg" alt="">
                </button>
            </div>
            <div class="product__title">
                <a href="<?=$favour['DETAIL_PAGE_URL']?>">
                    <?=$favour['NAME']?>
                </a>
            </div>
            <div class="product__category">
                <?php
                $sections = CIBlockSection::GetList(array(), Array('IBLOCK_ID' => 2, 'ACTIVE' => 'Y', 'ID' => $favour['IBLOCK_SECTION_ID']), false, array('NAME'),
                    false);
                $section = $sections->GetNext();
                ?>
                <?=$section['NAME']?>
            </div>
            <div class="product__price">
                <?=getPriceWithDiscount($favour['ID'], $favour['CATALOG_PRICE_1'], $groups)?> ₽
            </div>
            <?php if(isSku(2, $favour['ID']) !== 1):?>
                    <button class="product__basket add-cart-ajax" data-cart="<?=$favour['ID']?>">
                        <img src="<?=SITE_TEMPLATE_PATH?>/img/icon/bag2.svg" alt="">
                    </button>
                <?php else:?>
                    <button class="product__basket">
                        <a href="<?=$favour['DETAIL_PAGE_URL']?>"><img src="<?=SITE_TEMPLATE_PATH?>/img/icon/bag2.svg" alt=""></a>
                    </button>
            <?php endif;?>
        </article>

    <?php endwhile;
    endif;
    ?>

