<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if(!empty($arResult['ITEMS'])):
    ?><div class="delivery__list"><?
	    foreach($arResult['ITEMS'] as $arItem):
	    	?><div class="delivery__list-item"><?
	    		?><div class="delivery__list-item_name"><?= $arItem['NAME']; ?></div><?
	    		?><div class="delivery__list-item_desc"><?= html_entity_decode($arItem['DESCRIPTION']); ?></div><?
	    	?></div><?
		endforeach;
	?></div><?
endif
?>