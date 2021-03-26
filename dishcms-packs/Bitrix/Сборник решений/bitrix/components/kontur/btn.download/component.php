<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->RunDownload($arParams);

$arResult=array(
	'LINK'=>$this->GetLink($arParams),
	'LABEL'=>$arParams['LABEL']
);

$this->IncludeComponentTemplate();
?>