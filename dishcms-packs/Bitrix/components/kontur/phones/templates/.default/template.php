<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
?>
<ul>
	<? if($arParams['PHONE_NUMBER_1']): ?>
	<li>
		<?=$arParams['PHONE_NUMBER_1']?>
		<? if($arParams['PHONE_NUMBER_1_TEXT']): ?>
			<span><?= $arParams['PHONE_NUMBER_1_TEXT']; ?></span>
		<? endif; ?>
	</li>
	<? endif; ?>

	<? if($arParams['PHONE_NUMBER_2']): ?>
    <li>
        <?=$arParams['PHONE_NUMBER_2']?>
        <? if($arParams['PHONE_NUMBER_2_TEXT']): ?>
            <span><?= $arParams['PHONE_NUMBER_2_TEXT']; ?></span>
        <? endif; ?>
    </li>
    <? endif; ?>
</ul>
