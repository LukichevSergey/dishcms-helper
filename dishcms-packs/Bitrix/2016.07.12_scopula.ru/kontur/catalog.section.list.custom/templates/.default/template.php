<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
var_dump($arResult['ITEMS']);
if(!empty($arResult['ITEMS'])):
?><ul class="page__catalog-list"><?
    foreach($arResult['ITEMS'] as $arItem):
        if(!empty($arItem['IMAGE'])):
        ?><li<? if(!empty($arItem['SELECTED'])) echo ' class="selected"'; ?>><?
            if(!empty($arItem['LINK'])): ?><a href="<?=$arItem['LINK']?>"><?
            endif;
            if(!empty($arItem['IMAGE'])):
                ?><span class="page__catalog-list_item-image"><img src="<?=$arItem['IMAGE']?>" title="<?=$arItem['NAME']?>" /></span><?
            endif; ?><span><?=$arItem['NAME']?></span><?
            if(!empty($arItem['LINK'])): ?></a><? endif;
            if(!empty($arItem['SECTIONS'])):
                ?><ul class="arrow_box"><?
                foreach($arItem['SECTIONS'] as $arSection):
                ?><li<?
                    if(!empty($arSection['SELECTED'])) echo ' class="selected"';
                    ?>><a href="<?=$arSection['SECTION_PAGE_URL']?>"><?=$arSection['NAME']?></a></li><?
                endforeach;
                ?></ul><?
            endif;
        ?></li><?
        endif;
    endforeach;
    ?></ul><?
endif;
?>
