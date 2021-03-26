<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arResult=array(
	'LINK'=>$arParams['LINK'],
	'IMAGE'=>array(
		'SRC'=>$arParams['IMAGE_SRC'],
		'ALT'=>$arParams['IMAGE_ALT']
	)
);

$this->IncludeComponentTemplate();
?>