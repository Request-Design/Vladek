<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Избранное");

?>

    <main>

        <section class="favorites">
            <div class="container">
                <div class="title">
                    Избранное
                </div>
                <div class="favorites__setting">
                    <label class="label-search">
                        <input class="favorites__search" name="FAVOUR_SEARCH" placeholder="Поиск по товарам">
                    </label>

                    <div class="favorites__check">
                        <div class="checkbox">
                            <input type="checkbox" id="checkbox_1" class="available" name="AVAILABLE">
                            <label for="checkbox_1">снова в наличии</label>
                        </div>
                    </div>
                </div>
                <div class="favorites__wrapper">
                    <?php

                    if (!empty($arElements) || !empty($_SESSION['favourites'])):
                        if(!(empty($arElements))) $favouritesArr = $arElements;
                        else $favouritesArr = $_SESSION['favourites'];

                        $favourites = CIBlockElement::GetList(array(), Array('IBLOCK_ID' => 2, 'ACTIVE' => 'Y', 'ID' => $favouritesArr), false, array(),
                            array('ID', 'NAME', 'CODE', 'CATALOG_GROUP_1', 'IBLOCK_SECTION_ID', 'PREVIEW_PICTURE', 'QUANTITY', 'DETAIL_PAGE_URL'));
                        while($favour = $favourites->GetNext()):?>
                            <?php

                            $mxResult = \CCatalogSku::getOffersList($favour['ID'], 2, array('ACTIVE' => 'Y', '>QUANTITY' => 0), array('ID'));
                            if($mxResult) $quant = 'true';
                            else
                            {
                                if($favour['QUANTITY'] == 0) $quant = 'false';
                            }

                            ?>
                            <article class="product product-favour product-<?=$favour['ID']?> <?php if($quant == 'false') echo 'product--not'?>" >

                                <div class="product__image">
                                    <a href="<?=$favour['DETAIL_PAGE_URL']?>">
                                        <img src="<?=CFile::GetPath($favour['PREVIEW_PICTURE'])?>" alt="<?=$favour['NAME']?>">
                                    </a>
                                    <button class="product__heart">
                                        <img src="<?=SITE_TEMPLATE_PATH?>/img/icon/heart.svg" alt="heart">
                                    </button>
                                    <button class="product__close remove" data-favor="<?=$favour['ID']?>">
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


                </div>
            </div>
        </section>
    </main>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>