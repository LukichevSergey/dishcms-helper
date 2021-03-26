<?
/**
 * Установка заголовков
 * $APPLICATION->SetPageProperty("keywords", \planita\GetRegionMetaKey($arResult));
 * $APPLICATION->SetPageProperty("description", \planita\GetRegionMetaDesc($arResult));
 * $APPLICATION->SetPageProperty("title", \planita\GetRegionMetaTitle($arResult));
 * $APPLICATION->SetTitle(\planita\GetRegionMetaTitle($arResult));
 */
namespace planita;

function GetCityPrefix($domain=null)
{
	if(!$domain) {
		$domain=preg_replace('/^([^.]+)(\.planita\.ru)(.*?)$/', '$1', $_SERVER['SERVER_NAME']);
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
    	    $DESCRIPTION=$arResult["DESCRIPTION"];
	    }
	}
	return $DESCRIPTION;
}

function GetRegionMetaTitle($arResult, $bSetProperty=true, $bEmptySet=false, $bSetTitle=true) 
{
   	$value=\KonturGetSectionUFProp($arResult["IBLOCK_ID"], $arResult["ID"], CITY_PREFIX_UF."META_TITLE", true);
    if($bSetProperty && ($value || $bEmptySet)) {
        global $APPLICATION;
        $APPLICATION->SetPageProperty("title", $value);
        if($bSetTitle) $APPLICATION->SetTitle($value);
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
    return $value;
}

function GetRegionMetaDesc($arResult, $bSetProperty=true, $bEmptySet=false) 
{
   	$value=\KonturGetSectionUFProp($arResult["IBLOCK_ID"], $arResult["ID"], CITY_PREFIX_UF."META_DESC", true);
    if($bSetProperty && ($value || $bEmptySet)) {
        global $APPLICATION;
        $APPLICATION->SetPageProperty("description", $value);
    }
    return $value;
}
