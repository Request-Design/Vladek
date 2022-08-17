<?php 
CModule::IncludeModule('sale');

$basket = \Bitrix\Sale\Basket::loadItemsForFUser(\Bitrix\Sale\Fuser::getId(), Bitrix\Main\Context::getCurrent()->getSite());
$price = ceil($basket->getPrice());
$countProduct = array_sum($basket->getQuantityList());

foreach ($basket as $basketItem)
{
    $productsAr[] = ['POS_ID' => $basketItem->getField('ID'),'ID' => $basketItem->getField('PRODUCT_ID'), 'QUANTITY' => ceil($basketItem->getField('QUANTITY'))];
}
?> 

    <div class="quantity quantity-ajax">
      <button class="minus minus-ajax" type="button" title="-">-</button>
      <input  class="quant-data" data-pos="<?=$productsAr[$i]['POS_ID']?>" first-quant="<?=$productsAr[$i]['QUANTITY']?>" data-address="basket" max-quantity="<?=$product['QUANTITY']?>" data-quant="<?=$product['ID']?>" type="number" value="<?=$productsAr[$i]['QUANTITY']?>">
      <button class="plus plus-ajax"  type="button" title="+">+</button>
    </div>
