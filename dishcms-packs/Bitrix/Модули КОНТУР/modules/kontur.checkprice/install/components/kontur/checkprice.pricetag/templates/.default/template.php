<?
/** @var \Kontur\CheckPrice\Component\CheckPrice_TagPrice $component */
if(!defined('B_PROLOG_INCLUDED')||B_PROLOG_INCLUDED!==true)exit();

use Kontur\CheckPrice\PriceTagCollection;

\Bitrix\Main\UI\Extension::load("ui.buttons"); 

$pricetags=$component->getPriceTagList();
?>

<? if(empty($pricetags)) { ?>
    <p>не выбрано ни одного ценника для печати</p>
<? } else  { ?>
    <table id="kontur-chekcprice-table-tagprice-list" width="100%" class="kontur-chekcprice-table-tagprice-list">
        <thead>
            <tr>
                <th>Товар</th>
                <th>Цена</th>
                <th>&nbsp;</th>
            </tr>
        </thead>
        <tbody>
        <? $odd=true; ?>
        <? foreach($pricetags as $productId=>$product) { $trCssClass=$odd?'odd':'even'; ?>
            <? if(empty($product['OFFERS'])) { ?>
                <tr class="<?=$trCssClass?>">
                    <td>
                        <?= $product['NAME']; ?>
                        <? if(!empty($product['DETAIL_PAGE_URL'])) { ?>
                            <span style="white-space:nowrap"> ( <a href="<?=$product['DETAIL_PAGE_URL']?>" target="_blank">перейти</a> )</span>
                        <? } ?>
                    </td>
                    <td>
                        <?= \CurrencyFormat($product['PRICE'], $product['CURRENCY']); ?>
                    </td>
                    <td>
                        <? if($product['IS_PRICETAG']) { ?>
                            <button class="ui-btn ui-btn-xs ui-btn-danger-light js-kontur-checkprice-pricetag-btn-remove" 
                                data-item="<?=$productId?>">Убрать</button>
                        <? } else { ?>
                            &nbsp;
                        <? } ?>
                    </td>
                </tr>
            <? } else { ?>
                <tr class="<?=$trCssClass?>" data-product="<?=$productId?>">
                    <td colspan="3" class="product-with-offers-heading">
                        <?= $product['NAME']; ?>
                        <? if(!empty($product['DETAIL_PAGE_URL'])) { ?>
                            <span style="white-space:nowrap"> ( <a href="<?=$product['DETAIL_PAGE_URL']?>" target="_blank">перейти</a> )</span>
                        <? } ?>
                    </td>
                </tr>
                <? foreach($product['OFFERS'] as $offerId=>$offer) { ?>
                    <tr class="<?=$trCssClass?>" data-product="<?=$productId?>">
                        <td class="offer-heading">
                            <?= $offer['NAME']; ?>
                        </td>
                        <td>
                            <?= isset($offer['PRICE']) ? \CurrencyFormat($offer['PRICE'], $offer['CURRENCY']) : '&nbsp;'; ?>
                        </td>
                        <td>
                            <? if($offer['IS_PRICETAG']) { ?>
                                <button class="ui-btn ui-btn-xs ui-btn-danger-light js-kontur-checkprice-pricetag-btn-remove" 
                                    data-item="<?=$offerId?>">Убрать</button>
                            <? } else { ?>
                                &nbsp;
                            <? } ?>
                        </td>
                    </tr>
                <? } ?>
            <? } ?>
        <? $odd=!$odd; } ?>
        </tbody>
    </table>
    <div class="kontur-checkprice-bottom-panel">
        <button class="ui-btn ui-btn-primary js-kontur-checkprice-pricetag-btn-print">Распечатать</button>
    </div>
    <script>new window.konturCheckPricePriceTagComponent(<?=\CUtil::PhpToJSObject([
        'sess'=>bitrix_sessid_get()
    ]); ?>);</script>
<? } ?>