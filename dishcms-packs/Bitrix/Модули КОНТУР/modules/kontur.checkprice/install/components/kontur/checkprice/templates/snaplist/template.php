<?php
/** @var \Kontur\CheckPrice\Component\CheckPrice $component */
if(!defined('B_PROLOG_INCLUDED')||B_PROLOG_INCLUDED!==true)exit();

use Kontur\CheckPrice\PriceTagCollection;

\Bitrix\Main\UI\Extension::load("ui.buttons");

if(empty($arParams['SNAP_2'])) {
    $data=$component->getActualSnapData($arParams['SNAP_1']);
}
else {
    $data=$component->getSnap2SnapData($arParams['SNAP_1'], $arParams['SNAP_2']);
}
?>
<? if(empty($data)) { ?>
    <? echo \CAdminMessage::ShowMessage('Изменений в цене товаров не было'); ?>
<? } else { ?>
    <table width="100%" class="kontur-chekcprice-table-snap-list">
        <thead>
            <tr>
                <th>Наименование товара</th>
                <th>Старая цена</th>
                <th>Новая цена</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
            </tr>
        </thead>
        <tbody>
        <? $odd=true; ?>
        <? foreach($data as $productId=>$product) { $trCssClass=$odd?'odd':'even'; ?>
            <? if(empty($product['OFFERS'])) { ?>
                <tr class="<?=$trCssClass?>">
                    <td>
                        <?= $product['NAME']; ?>
                    </td>
                    <td>
                        <? if($product['OLD_PRICE']) { ?>
                            <?= \CurrencyFormat($product['OLD_PRICE'], 'RUB'); ?>
                        <? } else { ?>
                            <?= ShowNote('новый'); ?>
                        <? } ?>
                    </td>
                    <td>
                        <?= \CurrencyFormat($product['NEW_PRICE'], 'RUB'); ?>
                    </td>
                    <td>
                        <? if(!empty($product['DETAIL_PAGE_URL'])) { ?>
                            <a href="<?=$product['DETAIL_PAGE_URL']?>" target="_blank">перейти к товару</a>
                        <? } ?>
                    </td>
                    <td>
                        <? $priceTagAdded=PriceTagCollection::getInstance()->exists($productId); ?>
                        <button class="ui-btn ui-btn-xs js-kontur-checkprice-btn-pricetag<? if($priceTagAdded) echo ' ui-btn-success-light'; ?>" 
                            data-added="<?=$priceTagAdded ? 'Y' : 'N'; ?>" 
                            data-item="<?=$productId?>"><?=$priceTagAdded ? 'Ценник добавлен' : 'Добавить ценник'; ?></button>
                    </td>
                </tr>
            <? } else { ?>
                <tr class="<?=$trCssClass?>">
                    <td colspan="3" class="product-with-offers-heading">
                        <?= $product['NAME']; ?>
                    </td>
                    <td class="product-with-offers-heading">
                        <? if(!empty($product['DETAIL_PAGE_URL'])) { ?>
                            <a href="<?=$product['DETAIL_PAGE_URL']?>" target="_blank">перейти к товару</a>
                        <? } ?>
                    </td>
                    <td class="product-with-offers-heading">
                        &nbsp;
                    </td>
                </tr>
                <? foreach($product['OFFERS'] as $offerId=>$offer) { ?>
                    <tr class="<?=$trCssClass?>">
                        <td class="offer-heading">
                            <?= $offer['NAME']; ?>
                        </td>
                        <td>
                            <? if($offer['OLD_PRICE']) { ?>
                                <?= \CurrencyFormat($offer['OLD_PRICE'], 'RUB'); ?>
                            <? } else { ?>
                                <?= ShowNote('новый'); ?>
                            <? } ?>
                        </td>
                        <td>
                            <?= isset($offer['NEW_PRICE']) ? \CurrencyFormat($offer['NEW_PRICE'], 'RUB') : '&nbsp;'; ?>
                        </td>
                        <td>
                            &nbsp;
                        </td>
                        <td>
                            <? $priceTagAdded=PriceTagCollection::getInstance()->exists($offerId); ?>
                            <button class="ui-btn ui-btn-xs js-kontur-checkprice-btn-pricetag<? if($priceTagAdded) echo ' ui-btn-success-light'; ?>" 
                                data-added="<?=$priceTagAdded ? 'Y' : 'N'; ?>" 
                                data-item="<?=$offerId?>"><?=$priceTagAdded ? 'Ценник добавлен' : 'Добавить ценник'; ?></button>
                        </td>
                    </tr>
                <? } ?>
            <? } ?>
        <? $odd=!$odd; } ?>
        </tbody>
    </table>
<? } ?>
<style>.kontur-chekcprice-table-snap-list td {
    padding: 2px 10px;
    border: 1px solid #c4ced2 !important;
    font-size: 13px;
}
.kontur-chekcprice-table-snap-list p {
    margin: 0;
    font-size: 13px;
}
.kontur-chekcprice-table-snap-list td.product-with-offers-heading {
    padding: 5px 10px;
    font-weight: bold;
}
.kontur-chekcprice-table-snap-list td.product-with-offers-heading a {
    font-weight: normal;
}
.kontur-chekcprice-table-snap-list td.offer-heading {
    padding-left: 40px;
}
.kontur-chekcprice-table-snap-list tbody td:not(:first-child) {
    width:15%;
    text-align:right;
}
.kontur-chekcprice-table-snap-list tbody td:first-child {
    text-align:left;
}
.kontur-chekcprice-table-snap-list tbody td:nth-last-child(2) {
    text-align:center;
}
.kontur-chekcprice-table-snap-list tr {
    background: #e9f0f2;
}
.kontur-chekcprice-table-snap-list tr.even {
    background: #f5f9f9;
}
.kontur-chekcprice-table-snap-list thead tr {
    background: #dce7ed;
}
.kontur-chekcprice-table-snap-list thead th {
    padding: 5px;
    text-transform: uppercase;
    border: 1px solid #c4ced2;
    text-align: left;
}
.kontur-chekcprice-table-snap-list thead th:not(:first-child) {
    text-align: right;
    padding-right: 10px;
}
</style>