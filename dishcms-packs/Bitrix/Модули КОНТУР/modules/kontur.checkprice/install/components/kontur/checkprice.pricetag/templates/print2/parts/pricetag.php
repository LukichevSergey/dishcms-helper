<?php
if(!defined('B_PROLOG_INCLUDED')||B_PROLOG_INCLUDED!==true)exit();
/** @var [] $item */

$isSale=(sprintf('%.02f', $item['PRICE']) != sprintf('%.02f', $item['OLD_PRICE'])); 
$price=intval($item['PRICE']);
$priceKop=sprintf("%'.02d", ceil(fmod(floatval($item['PRICE']), 1) * 100));
?>
<div class="_price-block<? if($isSale && ($price > 99999)) echo ' big-price'; ?>">
    <div class="_header-top">
        Чакалов Сергей Анатольевич (ИП)
    </div>
    <header class="_header">
        <div class="_left-content">
            <h3><?= $item['NAME'] ?? null; ?></h3>
        </div>
        <div class="_right-content">
            <div class="_date"><?= date('d.m.Y'); ?></div>
            <? if($isSale) { 
                $oldPrice=intval($item['OLD_PRICE']);
                $oldPriceKop=sprintf("%'.02d", ceil(fmod(floatval($item['OLD_PRICE']), 1) * 100));
                ?>
                <h6>Старая цена:</h6>
                <div class="_price _price-old">
                    <span><?= $oldPrice; ?></span>
                    <div class="_postfix">
                        <span class="_num"><?= $oldPriceKop; ?></span>
                        <span class="_string"></span>
                        <span class="_currency">руб.</span>
                    </div>
                </div>
            <? } ?>
        </div>
    </header>

    <main class="_main">
        <? if($isSale) { ?>
        <div class="_mark">
            <span>Акция!</span>
        </div>
        <? } else { ?>
            <div></div>
        <? } ?>
        <div class="_price _price-new">
            <span><?= $price; ?></span>
            <div class="_postfix">
                <span class="_num"><?= $priceKop; ?></span>
                <span class="_string"></span>
                <span class="_currency">руб.</span>
            </div>
        </div>
    </main>

    <div class="_footer">
        <div class="_code">
            Код: <?= $item['PROPERTIES']['CML2_ARTICLE'] ?? $item['PROPERTIES']['SKU'] ?? null; ?>
        </div>
        <div class="_price-for-one">
            Цена за: <?= $item['MEASURE'] ?? 'шт'; ?>.
        </div>
    </div>

    <div class="_footer-bottom">
        Подпись ответственного лица:
    </div>
</div>