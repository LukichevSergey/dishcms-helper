<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(!empty($arResult['ITEMS'])):
?>
	<ul class="kontur_menu_section_list_ext">
	<? foreach($arResult['ITEMS'] as $arItem): ?>
		<li<? if(!empty($arItem['SELECTED'])) echo ' class="selected"'; ?>><?
			if(!empty($arItem['LINK'])): ?><a href="<?=$arItem['LINK']?>"><? endif;
			?><?=$arItem['NAME']?><?
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
		?></li>
	<? endforeach; ?>
	</ul>
<? endif; ?>
