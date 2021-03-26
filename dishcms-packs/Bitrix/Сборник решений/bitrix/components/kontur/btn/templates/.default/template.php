<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?
if(empty($arParams['LINK'])):
    ?><a href="<?=$arParams['LINK']?>" class="<?=$arParams['CSS_CLASS']?>"><span><?=$arParams['LABEL']?></span></a><?
else: 
    ?><span class="<?=$arParams['CSS_CLASS']?>"><?=$arParams['LABEL']?></span><?
endif?>
