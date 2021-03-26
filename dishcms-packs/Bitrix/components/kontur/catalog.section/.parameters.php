<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!\Bitrix\Main\Loader::includeModule("iblock"))
    return;

if(!\Bitrix\Main\Loader::includeModule("catalog"))
	return;

$arPrice=array();
$arSort = array_merge($arSort, CCatalogIBlockParameters::GetCatalogSortFields());
$rsPrice=CCatalogGroup::GetListEx(
    array("SORT" => "ASC"),
    array(),
    false,
    false,
	array('ID', 'NAME', 'NAME_LANG')
);
while($arr=$rsPrice->Fetch())
	$arPrice[$arr["NAME"]] = "[".$arr["NAME"]."] ".$arr["NAME_LANG"];

$arComponentParameters = array(
	"GROUPS" => array(
		"ELEMENTS" => array(
			"NAME" => GetMessage('KONTUR_CS_ELEMENTS_GROUP_NAME'),
			"SORT" => 500
		)
	),
	"PARAMETERS" => array(
		"PRICE_CODE" => array(
            "PARENT" => "ELEMENTS",
            "NAME" => GetMessage("KONTUR_CS_PRICE_CODE_NAME"),
            "TYPE" => "LIST",
            "MULTIPLE" => "Y",
            "SIZE" => (count($arPrice) > 5 ? 8 : 3),
            "VALUES" => $arPrice,
        ),
		"BASKET_URL" => array(
			"PARENT"=>"ELEMENTS",
			"NAME"=>"Ссылка на ajax-скрипт добавления в корзину",
			"TYPE"=>"STRING",
			"DEFAULT"=>"/ajax/basket.php"
		)
	)
);
?>
