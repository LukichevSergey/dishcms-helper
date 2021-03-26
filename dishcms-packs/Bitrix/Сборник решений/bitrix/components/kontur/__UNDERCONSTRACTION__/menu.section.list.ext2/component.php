<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arResult['ITEMS']=$this->GetMenuItems($arParams);

$this->IncludeComponentTemplate();
?>
