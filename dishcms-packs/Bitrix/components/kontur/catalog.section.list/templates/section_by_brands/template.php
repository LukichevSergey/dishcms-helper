<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
?>
<? if ((0 < $arResult["SECTIONS_COUNT"]) && !empty($arResult['ELEMENTS_COUNT'])): ?>
<div class="products-category">
    <div class="products-title"><?= $arParams['LIST_NAME']; ?></div>
    <ul class="products-category__list">
        <? foreach ($arResult['SECTIONS'] as $arSection): ?>
            <li class="products-category-list__item">
                <a href="<?= $arSection['SECTION_PAGE_URL']; ?>"><?= $arSection['NAME']; ?></a>
                <span>(<?= $arSection['ELEMENT_CNT']; ?>)</span>
            </li>
        <? endforeach; ?>
    </ul>
</div>
<? endif; ?>
<div class="products-filter">
    <div class="filter">
        <div class="filter-button">
            <div class="filter-result">Всего товаров <span><?= number_format(($arResult['ELEMENTS_COUNT'] ?: 0), 0, '.', ' '); ?></span></div>
        </div>
    </div>
</div>

