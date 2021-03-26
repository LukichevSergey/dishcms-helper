<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
if(!empty($arResult['TABS'])):
?><div class="tabs-box">
	<div class="tabs"><?
   		$i=0;
   		foreach($arResult['TABS'] as $arTab):
   			?><div class="tabs_item<? if(!$i++) echo ' active'; ?>"><a href="javascript:;"><?= $arTab['NAME']; ?></a></div><?
   		endforeach;
   	?></div>
   	<div class="tabs__cont"><?
   		$i=0;
   		foreach($arResult['TABS'] as $arTab):
   			?><div class="tabs__cont_item<? if(!$i++) echo ' active'; ?>"><?
   				$APPLICATION->IncludeFile($arTab['FILE'], array('SHOW_BORDER'=>false));
   			?></div><?
   		endforeach;
   	?></div>
</div><?
endif;
?>
