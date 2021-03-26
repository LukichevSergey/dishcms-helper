<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arResult['ITEMS']=$APPLICATION->IncludeComponent("bitrix:menu.sections","",Array(
	"IS_SEF" => "Y",
    "SEF_BASE_URL" => "/catalog/",
    "SECTION_PAGE_URL" => "#SECTION_CODE_PATH#/",
    "DETAIL_PAGE_URL" => "#SECTION_CODE_PATH#/#ELEMENT_CODE#",
    "IBLOCK_TYPE" => $arParams['IBLOCK_TYPE'],
    "IBLOCK_ID" => $arParams['IBLOCK_ID'],
    "DEPTH_LEVEL" => $arParams['DEPTH_LEVEL'],
    "CACHE_TYPE" => "A",
    "CACHE_TIME" => "3600"
));

$this->IncludeComponentTemplate();
?>