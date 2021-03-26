<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if(!empty($arResult['IMAGE']['SRC'])):?>
	<?if(!empty($arResult['LINK'])):?><a href="<?=$arResult['LINK']?>"><?endif
		?><img src="<?=$arResult['IMAGE']['SRC']?>" alt="<?=$arResult['IMAGE']['ALT']?>" title="<?=$arResult['IMAGE']['ALT']?>"><?
	if(!empty($arResult['LINK'])):?></a><?endif?>
<?endif?>
