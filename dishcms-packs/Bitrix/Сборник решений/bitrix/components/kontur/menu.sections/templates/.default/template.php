<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<span class="nav__catalog-icon js-menu-catalog-popup"><img src="<?=SITE_TEMPLATE_PATH?>/images/icons/menu.png" alt="Каталог" title="Каталог" /></span>
<? if(!empty($arResult['ITEMS'])): ?>
<div class="menu-catalog js-menu-catalog" style="display:none">
    <span class="nav__catalog-top_header">Каталог</span>
    <ul class="nav__catalog-top_level-1"><?
    $depthLevel=1;
    foreach($arResult['ITEMS'] as $arLink) {
        if($depthLevel > $arLink[3]['DEPTH_LEVEL']) {
            echo str_repeat('</li></ul>', $depthLevel-$arLink[3]['DEPTH_LEVEL']);
            $depthLevel=$arLink[3]['DEPTH_LEVEL'];
        }
        elseif($depthLevel < $arLink[3]['DEPTH_LEVEL']) {
            ?><ul class="nav__catalog-top_level-<?=$arLink[3]['DEPTH_LEVEL']?>"><?
            $depthLevel=$arLink[3]['DEPTH_LEVEL'];
        }
        else { 
        	echo str_repeat('</li>', $arLink[3]['DEPTH_LEVEL']-$depthLevel);
        }
    ?><li><a href="<?=$arLink[1]?>"<? if($APPLICATION->GetCurPage(false) == $arLink[1]) echo ' class="bx-active"'; ?>><?=$arLink[0]?></a><?
    }
    str_repeat('</li></ul>', $depthLevel-1);
?></ul>
</div>
<? endif; ?>