<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arResult['ITEMS']=$APPLICATION->IncludeComponent("bitrix:menu.sections","",Array(
	"IS_SEF" => "Y",
    "SEF_BASE_URL" => $arParams['SEF_BASE_URL'],
    "SECTION_PAGE_URL" => $arParams['SECTION_PAGE_URL'],
    "DETAIL_PAGE_URL" => $arParams['DETAIL_PAGE_URL'],
    "IBLOCK_TYPE" => $arParams['IBLOCK_TYPE'],
    "IBLOCK_ID" => $arParams['IBLOCK_ID'],
    "DEPTH_LEVEL" => $arParams['DEPTH_LEVEL'],
    "CACHE_TYPE" => "A",
    "CACHE_TIME" => "3600"
));

$this->IncludeComponentTemplate();
?>