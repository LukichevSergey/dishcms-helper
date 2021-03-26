<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!IsModuleInstalled("search"))
{
	ShowError(GetMessage("CC_BST_MODULE_NOT_INSTALLED"));
	return;
}

if(!isset($arParams["PAGE"]) || strlen($arParams["PAGE"])<=0)
	$arParams["PAGE"] = "#SITE_DIR#search/index.php";

$arResult["CATEGORIES"] = array();
$query = ltrim($_POST["q"]); 
if(
	!empty($query)
	&& $_REQUEST["ajax_call"] === "y"
	&& (
		!isset($_REQUEST["INPUT_ID"])
		|| $_REQUEST["INPUT_ID"] == $arParams["INPUT_ID"]
	)
	&& CModule::IncludeModule("search")
)
{
	CUtil::decodeURIComponent($query);

	$arResult["alt_query"] = "";
	if($arParams["USE_LANGUAGE_GUESS"] !== "N")
	{
		$arLang = CSearchLanguage::GuessLanguage($query);
		if(is_array($arLang) && $arLang["from"] != $arLang["to"])
			$arResult["alt_query"] = CSearchLanguage::ConvertKeyboardLayout($query, $arLang["from"], $arLang["to"]);
	}

	$arResult["query"] = $query;
	$arResult["phrase"] = stemming_split($query, LANGUAGE_ID);

	$hasError=empty($arResult["phrase"]);

	if(!$hasError) {
		$arSearchFileds=array('NAME', 'PREVIEW_TEXT', 'DETAIL_TEXT');
		$arSearchFilter=array('LOGIC'=>'AND');
		$arSearchFilter['IBLOCK_TYPE']='catalog';
		$arSearchFilter['IBLOCK_ID']=array(13);//,14);
		$arSearchFilter['ACTIVE']='Y';
		$isEmptyFilter=true;
		foreach($arResult['phrase'] as $word=>$freq) {
			if(strlen($word) < 3) continue;
			$isEmptyFilter=false;
			$filter=array('LOGIC'=>'OR');
			foreach($arSearchFileds as $field) {
				$filter[]=array('%'.$field => $word);
			}
			$arSearchFilter[]=$filter;
		}
	
		$hasError=$isEmptyFilter;

		if(!$hasError) {
			$dbFound=CIBlockElement::GetList(Array("CATALOG_AVAILABLE"=>"DESC"), $arSearchFilter, false, false, array('ID'));
		/*	$dbFound=CCatalogProduct::GetList(
    			array('QUANTITY'=>"desc"),
		    	$arSearchFilter
			);*/

			$arResult["DB_ITEMS"]=$dbFound;

			$arResult["FORM_ACTION"] = htmlspecialcharsbx(str_replace("#SITE_DIR#", SITE_DIR, $arParams["PAGE"]));
		}
	}
	
	$APPLICATION->RestartBuffer();

	if(!$hasError) { 
		$this->IncludeComponentTemplate('ajax');
	}

	require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_after.php");
	die();
}
else
{
	$arResult["FORM_ACTION"] = htmlspecialcharsbx(str_replace("#SITE_DIR#", SITE_DIR, $arParams["PAGE"]));
    $APPLICATION->AddHeadScript($this->GetPath().'/script.js');
    CUtil::InitJSCore(array('ajax'));
    $this->IncludeComponentTemplate();
}
?>
