<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
?>
<? if(!empty($arParams['SOC_TITLE'])): ?>
	<h4><?= $arParams['SOC_TITLE']; ?></h4>
<? endif; ?>
<ul>
	<? if($arParams['SOC_VK_URL']): ?>
	<li><a target="_blank" href="<?=$arParams['SOC_VK_URL']?>">VK</a></li>
	<? endif; ?>

	<? if($arParams['SOC_INSTAGRAM_URL']): ?>
    <li><a target="_blank" href="<?=$arParams['SOC_INSTAGRAM_URL']?>">Instagram</a></li>
    <? endif; ?>

 	<? if($arParams['SOC_FB_URL']): ?>
    <li><a target="_blank" href="<?=$arParams['SOC_FB_URL']?>">FB</a></li>
    <? endif; ?>

	<? if($arParams['SOC_OK_URL']): ?>
   	<li><a target="_blank" href="<?=$arParams['SOC_OK_URL']?>">OK</a></li>
   	<? endif; ?>

 	<? if($arParams['SOC_FLAMP_URL']): ?>
    <li><a target="_blank" href="<?=$arParams['SOC_FLAMP_URL']?>">Flamp</a></li>
    <? endif; ?>
</ul>
