<?php
$values=array_column($arResult['PROPERTIES'], 'NAME', 'CODE');
array_multisort($values, SORT_ASC, $arResult['PROPERTIES']);

$values=array_column($arResult['DISPLAY_PROPERTIES'], 'NAME');
array_multisort($values, SORT_ASC, $arResult['DISPLAY_PROPERTIES']);
