<?
/** @var \Kontur\CheckPrice\Component\CheckPrice_TagPrice $component */
if(!defined('B_PROLOG_INCLUDED')||B_PROLOG_INCLUDED!==true)exit();

$pricetags=$component->getPriceTagList(['sku', 'CML2_ARTICLE']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ценники <?= date('d.m.Y'); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <link href="<?= $this->GetFolder(); ?>/style.css" rel="stylesheet">    
</head>
<body>
<div class="_price-page-container">
<? if(empty($pricetags)) { ?>
    <p>не выбрано ни одного ценника для печати</p>
<? } else  { ?>
    <? foreach($pricetags as $productId=>$product) { ?>
        <? if(empty($product['OFFERS'])) { ?>
            <? $item=$product; ?>
            <? include(__DIR__ . '/parts/pricetag.php'); ?>
        <? } else { ?>
            <? foreach($product['OFFERS'] as $offerId=>$offer) { ?>
                <? $item=$offer; ?>
                <? include(__DIR__ . '/parts/pricetag.php'); ?>
            <? } ?>
        <? } ?>
    <? } ?>
<? } ?>
</div>
<script>document.addEventListener("DOMContentLoaded",function(){window.print();});</script>
</body>
</html>