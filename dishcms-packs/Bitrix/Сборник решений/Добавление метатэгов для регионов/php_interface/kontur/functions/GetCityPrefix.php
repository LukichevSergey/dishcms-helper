<?
/**
 * Установка заголовков
 * $APPLICATION->SetPageProperty("keywords", \kontur\GetRegionMetaKey($arResult));
 * $APPLICATION->SetPageProperty("description", \kontur\GetRegionMetaDesc($arResult));
 * $APPLICATION->SetPageProperty("title", \kontur\GetRegionMetaTitle($arResult));
 * $APPLICATION->SetTitle(\kontur\GetRegionMetaTitle($arResult));
 */
namespace kontur;

define('KONTUR_BASE_DOMAIN', '');
define('KONTUR_SET_GLOBALS', true);

function GetCityPrefix($domain=null)
{
	if(!$domain) {
		$domain=preg_replace('/^([^.]+)(\.'.KONTUR_BASE_DOMAIN.')(.*?)$/', '$1', $_SERVER['SERVER_NAME']);
	}

	if($domain) {
		$cities=GetCities();
		if(isset($cities[$domain])) {
			return $cities[$domain]['code'];
		}
	}

	return 'main';
}

function GetRegionDesc($arResult) 
{
	if(CITY_PREFIX=='main') {
    	$DESCRIPTION=$arResult["DESCRIPTION"];
	}
	else {
    	$DESCRIPTION=\KonturGetSectionUFProp($arResult["IBLOCK_ID"], $arResult["ID"], CITY_PREFIX_UF."DETAIL_TEXT", true);
	    if(empty($DESCRIPTION)) {
        	    $DESCRIPTION='';//$arResult["DESCRIPTION"];
	    }
	}
	return $DESCRIPTION;
}

function GetRegionMetaTitle($arResult, $bSetProperty=true, $bEmptySet=false, $bSetTitle=true) 
{ 
    $value=\KonturGetSectionUFProp($arResult["IBLOCK_ID"], $arResult["ID"], CITY_PREFIX_UF."META_TITLE", true);
    if(!$value) {
	$value=\KonturGetSectionUFProp($arResult["IBLOCK_ID"], $arResult["ID"], "TITLE", true);
    }
    if($bSetProperty && ($value || $bEmptySet)) {
        global $APPLICATION;
        $APPLICATION->SetPageProperty("title", $value);
        if($bSetTitle) $APPLICATION->SetTitle($value);
    }
    if(KONTUR_SET_GLOBALS) {
        $GLOBALS['PAGE_META_TITLE']=$value;
    }
    return $value;
}

function GetRegionMetaKey($arResult, $bSetProperty=true, $bEmptySet=false) 
{
    $value=\KonturGetSectionUFProp($arResult["IBLOCK_ID"], $arResult["ID"], CITY_PREFIX_UF."KEYWORDS", true);
    if($bSetProperty && ($value || $bEmptySet)) {
        global $APPLICATION;
        $APPLICATION->SetPageProperty("keywords", $value);
    }
    if(KONTUR_SET_GLOBALS) {
	$GLOBALS['PAGE_META_KEYWORDS']=$value;
    }
    return $value;
}

function GetRegionMetaDesc($arResult, $bSetProperty=true, $bEmptySet=false) 
{
    $value=\KonturGetSectionUFProp($arResult["IBLOCK_ID"], $arResult["ID"], CITY_PREFIX_UF."META_DESC", true);
    if(!$value) {
	$value=\KonturGetSectionUFProp($arResult["IBLOCK_ID"], $arResult["ID"], "META_DESCRIPTION", true);
    }
    if($bSetProperty && ($value || $bEmptySet)) {
        global $APPLICATION;
        $APPLICATION->SetPageProperty("description", $value);
    }
    if(KONTUR_SET_GLOBALS) {
	$GLOBALS['PAGE_META_DESCRIPTION']=$value;
    }
    return $value;
}
