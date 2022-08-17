<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

$statics = CIBlockElement::GetList(array(), array('IBLOCK_ID' => $langAr['EXARCHAT_STATIC_DATA'], 'ACTIVE' => 'Y'), false, array(),
    array('PROPERTY_MAIN_PAGE', 'PROPERTY_SEARCH_RESULT', 'PROPERTY_SEARCH', 'PROPERTY_RESULTS', 'PROPERTY_FIND',
        'PROPERTY_EXARCH_AFRICA', 'PROPERTY_FOUND'
    ));
$static = $statics->GetNext();

$APPLICATION->SetTitle($static['PROPERTY_EXARCH_AFRICA_VALUE']);

function uniqueMult($array, $key)
{
    $temp_array = array();
    $i = 0;
    $key_array = array();

    foreach ($array as $val) {
        if (!in_array($val[$key], $key_array)) {
            $key_array[$i] = $val[$key];
            $temp_array[$i] = $val;
        }
        $i++;
    }
    return $temp_array;
}


$usefulID = array($langAr['EXARCHAT_PHOTO'], $langAr['EXARCHAT_DOCS'], $langAr['EXARCHAT_NAD'], $langAr['EXARCHAT_DSA'], $langAr['EXARCHAT_PUBLICATIONS'], $langAr['EXARCHAT_VIDEO'], $langAr['EXARCHAT_NEWS'], $langAr['EXARCHAT_ACTUAL']);
if(!$_GET['hashtag'])
{

    $items = CIBlockElement::GetList(array(), array('ACTIVE' => 'Y', //Получаем элементы по поиску
        'IBLOCK_ID' => $usefulID,
        array(
            "LOGIC" => "OR",
            array('NAME' => '%' . $_GET['search'] . '%'),
            array('PROPERTY_HESHTEG' => getIDHashtagByName($_GET['search'])),
        ),
    ), false, false,
        array('ID', 'NAME', 'DETAIL_PAGE_URL', 'IBLOCK_ID', 'PREVIEW_TEXT'));
    while($item = $items->GetNext()) {$res[$item['ID']] = $item;}


    $items = CIBlockSection::GetList(array(), array('ACTIVE' => 'Y', //Получаем разделы по поиску
        'IBLOCK_ID' => $usefulID,
        'NAME' => '%' . $_GET['search'] . '%',
    ), false, array('ID'),
        false);
    while($item = $items->GetNext()) {$sections[] = $item['ID'];}

    $res = uniqueMult($res, 'ID');
}
else
{
    $items = CIBlockElement::GetList(array(), array('ACTIVE' => 'Y', //Получаем элементы по поиску
        'IBLOCK_ID' => $usefulID,
        'PROPERTY_HESHTEG' => $_GET['hashtag']
    ), false, false,
        array('ID', 'NAME', 'DETAIL_PAGE_URL', 'IBLOCK_ID', 'PREVIEW_TEXT'));
    while($item = $items->GetNext()) {$res[$item['ID']] = $item;}
}


$countStart = count($res);
$elementsCount = 12;
$pagin = ($_GET['PAGE_ID']) ? $_GET['PAGE_ID'] : 0;
$result = array_chunk($res, $elementsCount);

?>
<main class="main">
    <section class="poisk">
        <div class="container">
            <div class="poisk__inner">
                <div class="poisk__subtitle">
                    <a href="<?=$hrefLang?>/" class="poisk__subtitle-link"><?=$static['PROPERTY_MAIN_PAGE_VALUE']?></a>
                    <div class="poisk__subtitle-name"><?=$static['PROPERTY_SEARCH_RESULT_VALUE']?></div>
                </div>
                <div class="poisk__title title"><?=$static['PROPERTY_SEARCH_RESULT_VALUE']?></div>
                <form class="poisk__form">
                    <label>

                        <input type="text" placeholder="<?=$static['PROPERTY_SEARCH_VALUE']?>..." name="search"
                               <?php if($_GET['hashtag']):?>
                                    value="<?=$_GET['hashtag']?>"
                                <?php else:?>
                                    value="<?=$_GET['search']?>"
                                <?php endif;?>
                              >

                        <button type="submit"><?=$static['PROPERTY_FIND_VALUE']?></button>
                    </label>
                    <div><?=$static['PROPERTY_FOUND_VALUE']?>
                        <span><?=$countStart?></span> <?=$static['PROPERTY_RESULTS_VALUE']?>
                    </div>
                </form>
                <div class="poisk__wrap">
                    <div class="poisk__items">
                        <?php foreach ($result[$pagin] as $el):?>
                            <div class="poisk__item">
                                <a href="<?=$hrefLang.$el['DETAIL_PAGE_URL']?>"><div class="poisk__item-title"><?=$el['NAME']?></div></a>
                                <div class="poisk__item-text"><?=$el['PREVIEW_TEXT']?></div>
                                <a href="<?=$hrefLang.$el['DETAIL_PAGE_URL']?>" class="poisk__item-link link-red"></a>
                            </div>
                        <?php endforeach;?>

                    </div>
                    <div class="poisk__numbers">
                        <?php
                        if($countStart > $elementsCount):
                            $radius = 3;
                            $count = round($countStart / $elementsCount);?>
                                <?php for ( $i = 0; $i <= $count; $i++ ):
                                    if($i == $count && (count($result[$i]) == 0)) {$i--; break;} ?>
                                    <?php if(($i > $pagin - $radius && $i < $pagin + $radius) ):?> <!--Вывод остальных страниц-->
                                    <a href="<?=$hrefLang?>/search/?search=<?=$_GET['search'].'&hashtag='.$_GET['hashtag'].'&PAGE_ID='.$i?>" class="poisk__numbers-n <?php if($pagin == $i) echo 'active';?>" pagin-id="<?=$i?>">
                                      <?=$i+1?>
                                    </a>
                                <?php elseif($pagin == 0 && $i < 3):?>  <!--Вывод первой страницы-->
                                    <a href="<?=$hrefLang?>/search/?search=<?=$_GET['search'].'&hashtag='.$_GET['hashtag'].'&PAGE_ID='.$i?>" class="poisk__numbers-n <?php if($pagin == $i) echo 'active';?>" pagin-id="<?=$i?>">
                                        <?=$i+1?>
                                    </a>
                                <?php endif;?>

                                <?php endfor;?>

                                <?php if ($pagin < $i - 3 && $i > 2 && $count >= 3 && ($i != $count && $countStart % 4 != 0) ): ?> <!--Вывод троеточия в случае, если дальше ещё есть страницы-->
                                    <div>...</div>
                                <?php endif;?>

                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>


<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
