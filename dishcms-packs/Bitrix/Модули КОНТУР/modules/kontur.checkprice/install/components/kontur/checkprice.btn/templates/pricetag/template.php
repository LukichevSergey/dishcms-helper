<?
/** @var \Kontur\CheckPrice\Component\CheckPrice_TagPrice $component */
if(!defined('B_PROLOG_INCLUDED')||B_PROLOG_INCLUDED!==true)exit();

use Kontur\CheckPrice\PriceTagCollection;

if(!empty($arParams['ID'])) { ?>
<div class="product-button checkprice-button-pricetag-box">
    <? if(PriceTagCollection::getInstance()->exists($arParams['ID'])) { ?>
        <a class="js-checkprice-pricetag-btn-pricetag-pricetag pricetag-added" data-item="<?= $arParams['ID']; ?>" href="javascript:;">Убрать ценник</a>
    <? } else { ?>
        <a class="js-checkprice-pricetag-btn-pricetag-pricetag" data-item="<?= $arParams['ID']; ?>" href="javascript:;">Добавить ценник</a>
    <? } ?>
</div>
<script>new window.konturCheckPriceBtnComponentTemplatePriceTag(<?=\CUtil::PhpToJSObject([
    'sess'=>bitrix_sessid_get()
]); ?>);</script>
<? } ?>