<?
/** @var \Kontur\CheckPrice\Component\CheckPrice_TagPrice $component */
if(!defined('B_PROLOG_INCLUDED')||B_PROLOG_INCLUDED!==true)exit();

use Kontur\CheckPrice\Helper;
// use Kontur\CheckPrice\PriceTagCollection;

?>
<div class="checkprice-button-list-box">
    <a href="<?= Helper::getPriceTagListPageUrl();?>">Список ценников<? /* ?> (<span class="js-checkprice-pricetag-btn-list-count"><?= PriceTagCollection::getInstance()->count(); ?></span>)<? /**/ ?></a>
</div>
<? /* ?>
<script>new window.konturCheckPriceBtnComponentTemplateList(<?=\CUtil::PhpToJSObject([
    'sess'=>bitrix_sessid_get()
]); ?>);</script>
<? /**/ ?>