<?php
/** @var \Kontur\CheckPrice\Component\CheckPrice $component */
if(!defined('B_PROLOG_INCLUDED')||B_PROLOG_INCLUDED!==true)exit();

use Kontur\CheckPrice\Helper;
use Kontur\CheckPrice\PriceTagCollection;

\Bitrix\Main\UI\Extension::load("ui.buttons"); 

if(empty($arParams['SNAP_1']) || empty($arParams['SNAP_2'])) {
    return false;
}

$data=$component->getSnap2SnapData($arParams['SNAP_1'], $arParams['SNAP_2']);
if(empty($data)) {
    return false;
}
?>
<table style="width:auto;">
    <thead>
        <tr style="background:#dce7ed;">
            <th style="padding:5px;text-transform:uppercase;border:1px solid #c4ced2;text-align:left;font-size:14px;">Наименование товара</th>
            <th style="padding:5px;text-transform:uppercase;border:1px solid #c4ced2;text-align:right;font-size:14px;padding-right:10px;">Старая цена</th>
            <th style="padding:5px;text-transform:uppercase;border:1px solid #c4ced2;text-align:right;font-size:14px;padding-right:10px;">Новая цена</th>
            <th style="padding:5px;text-transform:uppercase;border:1px solid #c4ced2;text-align:left;font-size:14px;">&nbsp;</th>
        </tr>
    </thead>
    <tbody>
    <? $odd=true; ?>
    <? foreach($data as $productId=>$product) { $trCssStyle=$odd?'background:#e9f0f2':'background:#f5f9f9;'; ?>
        <? if(empty($product['OFFERS'])) { ?>
            <tr style="<?=$trCssStyle?>">
                <td style="padding:2px 10px;border:1px solid #c4ced2 !important;font-size: 13px;">
                    <?= $product['NAME']; ?>
                </td>
                <td style="padding:2px 10px;border:1px solid #c4ced2 !important;font-size: 13px;width:15%;text-align:right;">
                    <? if($product['OLD_PRICE']) { ?>
                        <?= \CurrencyFormat($product['OLD_PRICE'], 'RUB'); ?>
                    <? } else { ?>
                        <span>новый</span>
                    <? } ?>
                </td>
                <td style="padding:2px 10px;border:1px solid #c4ced2 !important;font-size: 13px;width:15%;text-align:right;">
                    <?= \CurrencyFormat($product['NEW_PRICE'], 'RUB'); ?>
                </td>
                <td style="padding:2px 10px;border:1px solid #c4ced2 !important;font-size: 13px;width:15%;text-align:center;">
                    <? if(!empty($product['DETAIL_PAGE_URL'])) { ?>
                        <a style="white-space:nowrap;" href="<?=$product['DETAIL_PAGE_URL']?>" target="_blank">перейти к товару</a>
                    <? } ?>
                </td>
            </tr>
        <? } else { ?>
            <tr style="<?=$trCssStyle?>">
                <td colspan="3" style="padding:5px 10px;font-weight:bold;border:1px solid #c4ced2 !important;font-size: 13px;">
                    <?= $product['NAME']; ?>
                </td>
                <td style="padding:5px 10px;font-weight:bold;border:1px solid #c4ced2 !important;font-size: 13px;width:15%;text-align:center;">
                    <? if(!empty($product['DETAIL_PAGE_URL'])) { ?>
                        <a style="white-space:nowrap;" href="<?=$product['DETAIL_PAGE_URL']?>" target="_blank">перейти к товару</a>
                    <? } ?>
                </td>
            </tr>
            <? foreach($product['OFFERS'] as $offerId=>$offer) { ?>
                <tr style="<?=$trCssStyle?>">
                    <td style="padding:2px 10px;padding-left:40px;border:1px solid #c4ced2 !important;font-size: 13px;">
                        <?= $offer['NAME']; ?>
                    </td>
                    <td style="padding:2px 10px;border:1px solid #c4ced2 !important;font-size: 13px;width:15%;text-align:right;">
                        <? if($offer['OLD_PRICE']) { ?>
                            <?= \CurrencyFormat($offer['OLD_PRICE'], 'RUB'); ?>
                        <? } else { ?>
                            <span>новый</span>
                        <? } ?>
                    </td>
                    <td style="padding:2px 10px;border:1px solid #c4ced2 !important;font-size: 13px;width:15%;text-align:right;">
                        <?= isset($offer['NEW_PRICE']) ? \CurrencyFormat($offer['NEW_PRICE'], 'RUB') : '&nbsp;'; ?>
                    </td>
                    <td style="padding:2px 10px;border:1px solid #c4ced2 !important;font-size: 13px;width:15%;text-align:center;">
                        &nbsp;
                    </td>
                </tr>
            <? } ?>
        <? } ?>
    <? $odd=!$odd; } ?>
    </tbody>
</table>