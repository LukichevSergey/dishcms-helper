<?
//<title>CSV SalePrice</title>
/** @global int $line_num */
/** @global int $correct_lines */
/** @global int $error_lines */
/** @global string $tmpid */

/** @global int $IBLOCK_ID */
/** @global array $arIBlock */
/** @global string $first_names_r */
/** @global string $first_names_f */
/** @global int $CUR_FILE_POS */
/** @global string $USE_TRANSLIT */
/** @global string $TRANSLIT_LANG */
/** @global string $USE_UPDATE_TRANSLIT */
/** @global string $PATH2IMAGE_FILES */
/** @global string $outFileAction */

use Bitrix\Main,
	Bitrix\Catalog,
	Bitrix\Iblock;

IncludeModuleLangFile($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/catalog/import_setup_templ.php');
$startImportExecTime = getmicrotime();

global $USER;
global $APPLICATION;
$bTmpUserCreated = false;
if (!CCatalog::IsUserExists())
{
	$bTmpUserCreated = true;
	if (isset($USER))
		$USER_TMP = $USER;
	$USER = new CUser();
}

$strImportErrorMessage = "";
$strImportOKMessage = "";

global
	$arCatalogAvailProdFields,
	$defCatalogAvailProdFields,
	$arCatalogAvailPriceFields,
	$defCatalogAvailPriceFields,
	$arCatalogAvailValueFields,
	$defCatalogAvailValueFields,
	$arCatalogAvailQuantityFields,
	$defCatalogAvailQuantityFields,
	$arCatalogAvailGroupFields,
	$defCatalogAvailGroupFields,
	$defCatalogAvailCurrencies;

if (!isset($arCatalogAvailProdFields))
	$arCatalogAvailProdFields = CCatalogCSVSettings::getSettingsFields(CCatalogCSVSettings::FIELDS_ELEMENT);
if (!isset($arCatalogAvailPriceFields))
	$arCatalogAvailPriceFields = CCatalogCSVSettings::getSettingsFields(CCatalogCSVSettings::FIELDS_CATALOG);
if (!isset($arCatalogAvailValueFields))
	$arCatalogAvailValueFields = CCatalogCSVSettings::getSettingsFields(CCatalogCSVSettings::FIELDS_PRICE);
if (!isset($arCatalogAvailQuantityFields))
	$arCatalogAvailQuantityFields = CCatalogCSVSettings::getSettingsFields(CCatalogCSVSettings::FIELDS_PRICE_EXT);
if (!isset($arCatalogAvailGroupFields))
	$arCatalogAvailGroupFields = CCatalogCSVSettings::getSettingsFields(CCatalogCSVSettings::FIELDS_SECTION);

if (!isset($defCatalogAvailProdFields))
	$defCatalogAvailProdFields = CCatalogCSVSettings::getDefaultSettings(CCatalogCSVSettings::FIELDS_ELEMENT);
if (!isset($defCatalogAvailPriceFields))
	$defCatalogAvailPriceFields = CCatalogCSVSettings::getDefaultSettings(CCatalogCSVSettings::FIELDS_CATALOG);
if (!isset($defCatalogAvailValueFields))
	$defCatalogAvailValueFields = CCatalogCSVSettings::getDefaultSettings(CCatalogCSVSettings::FIELDS_PRICE);
if (!isset($defCatalogAvailQuantityFields))
	$defCatalogAvailQuantityFields = CCatalogCSVSettings::getDefaultSettings(CCatalogCSVSettings::FIELDS_PRICE_EXT);
if (!isset($defCatalogAvailGroupFields))
	$defCatalogAvailGroupFields = CCatalogCSVSettings::getDefaultSettings(CCatalogCSVSettings::FIELDS_SECTION);
if (!isset($defCatalogAvailCurrencies))
	$defCatalogAvailCurrencies = CCatalogCSVSettings::getDefaultSettings(CCatalogCSVSettings::FIELDS_CURRENCY);

$NUM_CATALOG_LEVELS = intval(COption::GetOptionString("catalog", "num_catalog_levels"));

$max_execution_time = intval($max_execution_time);
if ($max_execution_time <= 0)
	$max_execution_time = 0;
if (defined('BX_CAT_CRON') && true == BX_CAT_CRON)
	$max_execution_time = 0;

if (defined("CATALOG_LOAD_NO_STEP") && CATALOG_LOAD_NO_STEP)
	$max_execution_time = 0;

$separateSku = (string)Main\Config\Option::get('catalog', 'show_catalog_tab_with_offers') === 'Y';

$bAllLinesLoaded = true;

$io = CBXVirtualIo::GetInstance();

if (!function_exists('CSVCheckTimeout'))
{
	function CSVCheckTimeout($max_execution_time)
	{
		return ($max_execution_time <= 0) || (getmicrotime()-START_EXEC_TIME <= (2*$max_execution_time/3));
	}
}

$DATA_FILE_NAME = "";

if (strlen($URL_DATA_FILE) > 0)
{
	$URL_DATA_FILE = Rel2Abs("/", $URL_DATA_FILE);
	if (file_exists($_SERVER["DOCUMENT_ROOT"].$URL_DATA_FILE) && is_file($_SERVER["DOCUMENT_ROOT"].$URL_DATA_FILE))
		$DATA_FILE_NAME = $URL_DATA_FILE;
}

if (strlen($DATA_FILE_NAME) <= 0)
	$strImportErrorMessage .= GetMessage("CATI_NO_DATA_FILE")."<br>";

$IBLOCK_ID = intval($IBLOCK_ID);
if ($IBLOCK_ID <= 0)
{
	$strImportErrorMessage .= GetMessage("CATI_NO_IBLOCK")."<br>";
}
else
{
	$arIBlock = CIBlock::GetArrayByID($IBLOCK_ID);
	if (false === $arIBlock)
	{
		$strImportErrorMessage .= GetMessage("CATI_NO_IBLOCK")."<br>";
	}
}

if ('' == $strImportErrorMessage)
{
	$bWorkflow = CModule::IncludeModule("workflow") && ($arIBlock["WORKFLOW"] != "N");

	$bIBlockIsCatalog = false;
	$arSku = false;
	$rsCatalogs = CCatalog::GetList(
		array(),
		array('IBLOCK_ID' => $IBLOCK_ID),
		false,
		false,
		array('IBLOCK_ID', 'PRODUCT_IBLOCK_ID', 'SKU_PROPERTY_ID')
	);
	if ($arCatalog = $rsCatalogs->Fetch())
	{
		$bIBlockIsCatalog = true;
		$arCatalog['IBLOCK_ID'] = (int)$arCatalog['IBLOCK_ID'];
		$arCatalog['PRODUCT_IBLOCK_ID'] = (int)$arCatalog['PRODUCT_IBLOCK_ID'];
		$arCatalog['SKU_PROPERTY_ID'] = (int)$arCatalog['SKU_PROPERTY_ID'];
		if (0 < $arCatalog['PRODUCT_IBLOCK_ID'] && 0 < $arCatalog['SKU_PROPERTY_ID'])
		{
			$arSku = $arCatalog;
		}
	}

	$csvFile = new CCSVData();
	$csvFile->LoadFile($_SERVER["DOCUMENT_ROOT"].$DATA_FILE_NAME);

	if ($fields_type!="F" && $fields_type!="R")
		$strImportErrorMessage .= GetMessage("CATI_NO_FILE_FORMAT")."<br>";
}

if ('' == $strImportErrorMessage)
{
	$arDataFileFields = array();
	$fields_type = (($fields_type=="F") ? "F" : "R" );

	$csvFile->SetFieldsType($fields_type);

	if ($fields_type == "R")
	{
		$first_names_r = (($first_names_r=="Y") ? "Y" : "N" );
		$csvFile->SetFirstHeader(($first_names_r=="Y") ? true : false);

		$delimiter_r_char = "";
		switch ($delimiter_r)
		{
			case "TAB":
				$delimiter_r_char = "\t";
				break;
			case "ZPT":
				$delimiter_r_char = ",";
				break;
			case "SPS":
				$delimiter_r_char = " ";
				break;
			case "OTR":
				$delimiter_r_char = substr($delimiter_other_r, 0, 1);
				break;
			case "TZP":
				$delimiter_r_char = ";";
				break;
		}

		if (strlen($delimiter_r_char) != 1)
			$strImportErrorMessage .= GetMessage("CATI_NO_DELIMITER")."<br>";

		if ('' == $strImportErrorMessage)
			$csvFile->SetDelimiter($delimiter_r_char);
	}
	else
	{
		$first_names_f = (($first_names_f=="Y") ? "Y" : "N" );
		$csvFile->SetFirstHeader(($first_names_f=="Y") ? true : false);

		if (strlen($metki_f) <= 0)
			$strImportErrorMessage .= GetMessage("CATI_NO_METKI")."<br>";

		if ('' == $strImportErrorMessage)
		{
			$arMetkiTmp = preg_split("/[\D]/i", $metki_f);

			$arMetki = array();
			for ($i = 0, $intCount = count($arMetkiTmp); $i < $intCount; $i++)
			{
				if (intval($arMetkiTmp[$i]) > 0)
				{
					$arMetki[] = intval($arMetkiTmp[$i]);
				}
			}

			if (!is_array($arMetki) || count($arMetki)<1)
				$strImportErrorMessage .= GetMessage("CATI_NO_METKI")."<br>";

			if ('' == $strImportErrorMessage)
				$csvFile->SetWidthMap($arMetki);
		}
	}

	if ('' == $strImportErrorMessage)
	{
		$bFirstHeaderTmp = $csvFile->GetFirstHeader();
		$csvFile->SetFirstHeader(false);
		if ($arRes = $csvFile->Fetch())
		{
			for ($i = 0, $intCount = count($arRes); $i < $intCount; $i++)
			{
				$arDataFileFields[$i] = $arRes[$i];
			}
		}
		else
		{
			$strImportErrorMessage .= GetMessage("CATI_NO_DATA")."<br>";
		}
		global $NUM_FIELDS;
		$NUM_FIELDS = count($arDataFileFields);
	}
}

if ('' == $strImportErrorMessage)
{
	$bFieldsPres = false;
	for ($i = 0; $i < $NUM_FIELDS; $i++)
	{
		if (strlen(${"field_".$i})>0)
		{
			$bFieldsPres = true;
			break;
		}
	}
	if (!$bFieldsPres)
		$strImportErrorMessage .= GetMessage("CATI_NO_FIELDS")."<br>";
}

if ('' == $strImportErrorMessage)
{
	$USE_TRANSLIT = (isset($USE_TRANSLIT) && 'Y' == $USE_TRANSLIT ? 'Y' : 'N');
	if ('Y' == $USE_TRANSLIT)
	{
		$boolOutTranslit = false;
		if (isset($arIBlock['FIELDS']['CODE']['DEFAULT_VALUE']))
		{
			if ('Y' == $arIBlock['FIELDS']['CODE']['DEFAULT_VALUE']['TRANSLITERATION']
				&& 'Y' == $arIBlock['FIELDS']['CODE']['DEFAULT_VALUE']['USE_GOOGLE'])
			{
				$boolOutTranslit = true;
			}
		}
		if (isset($arIBlock['FIELDS']['SECTION_CODE']['DEFAULT_VALUE']))
		{
			if ('Y' == $arIBlock['FIELDS']['SECTION_CODE']['DEFAULT_VALUE']['TRANSLITERATION']
				&& 'Y' == $arIBlock['FIELDS']['SECTION_CODE']['DEFAULT_VALUE']['USE_GOOGLE'])
			{
				$boolOutTranslit = true;
			}
		}
		if ($boolOutTranslit)
		{
			$USE_TRANSLIT = 'N';
			$strImportErrorMessage .= GetMessage("CATI_USE_CODE_TRANSLIT_OUT")."<br>";
		}
	}
	if ('Y' == $USE_TRANSLIT)
	{
		$TRANSLIT_LANG = (isset($TRANSLIT_LANG) ? strval($TRANSLIT_LANG) : '');
		if (!empty($TRANSLIT_LANG))
		{
			$rsTransLangs = CLanguage::GetByID($TRANSLIT_LANG);
			if (!($arTransLang = $rsTransLangs->Fetch()))
			{
				$TRANSLIT_LANG = '';
			}
		}
		if (empty($TRANSLIT_LANG))
		{
			$USE_TRANSLIT = 'N';
			$strImportErrorMessage .= GetMessage("CATI_CODE_TRANSLIT_LANG_ERR")."<br>";
		}
	}
	$updateTranslit = false;
	if ($USE_TRANSLIT == 'Y')
	{
		$updateTranslit = true;
		if (isset($USE_UPDATE_TRANSLIT) && $USE_UPDATE_TRANSLIT == 'N')
			$updateTranslit = false;
	}
}

$IMAGE_RESIZE = (isset($IMAGE_RESIZE) && 'Y' == $IMAGE_RESIZE ? 'Y' : 'N');
$CLEAR_EMPTY_PRICE = (isset($CLEAR_EMPTY_PRICE) && 'Y' == $CLEAR_EMPTY_PRICE ? 'Y' : 'N');
$CML2_LINK_IS_XML = (isset($CML2_LINK_IS_XML) && 'Y' == $CML2_LINK_IS_XML ? 'Y' : 'N');
if (empty($arSku))
	$CML2_LINK_IS_XML = 'N';

if ('' == $strImportErrorMessage)
{
	$currentUserID = $USER->GetID();

	$boolUseStoreControl = (COption::GetOptionString('catalog', 'default_use_store_control') == 'Y');
	$arDisableFields = array(
		'CP_QUANTITY' => true,
		'CP_PURCHASING_PRICE' => true,
		'CP_PURCHASING_CURRENCY' => true,
	);

	$arProductCache = array();
	$arPropertyListCache = array();
	$arSectionCache = array();
	$arElementCache = array();

	$csvFile->SetPos($CUR_FILE_POS);
	$arRes = $csvFile->Fetch();
	if ($CUR_FILE_POS<=0 && $bFirstHeaderTmp)
	{
		$arRes = $csvFile->Fetch();
	}

	$bs = new CIBlockSection();
	$el = new CIBlockElement();
	$bWasIterations = false;

	//Iblock\PropertyIndex\Manager::enableDeferredIndexing();
	//Catalog\Product\Sku::enableDeferredCalculation();
	
	if ($arRes)
	{
		$bWasIterations = true;
		if ($bFirstLoadStep)
		{
			$tmpid = md5(uniqid(""));
			$line_num = 0;
			$correct_lines = 0;
			$error_lines = 0;
			$killed_lines = 0;

			$arIBlockProperty = array();
			$arIBlockPropertyValue = array();
			$multiplePropertyValuesCheck = array();
			$bThereIsGroups = false;
			$bDeactivationStarted = false;
			$arProductGroups = array();
			$currentProductSection = [];
			$bUpdatePrice = 'N';
		}

		$boolTranslitElement = false;

		$boolTranslitSection = false;
		$arTranslitElement = array();
		$arTranslitSection = array();
		if ('Y' == $USE_TRANSLIT)
		{
			if (isset($arIBlock['FIELDS']['CODE']['DEFAULT_VALUE']))
			{
				$arTransSettings = $arIBlock['FIELDS']['CODE']['DEFAULT_VALUE'];
				$boolTranslitElement = ($arTransSettings['TRANSLITERATION'] == 'Y');
				$arTranslitElement = array(
					"max_len" => $arTransSettings['TRANS_LEN'],
					"change_case" => $arTransSettings['TRANS_CASE'],
					"replace_space" => $arTransSettings['TRANS_SPACE'],
					"replace_other" => $arTransSettings['TRANS_OTHER'],
					"delete_repeat_replace" => ($arTransSettings['TRANS_EAT'] == 'Y'),
					"use_google" => ($arTransSettings['USE_GOOGLE'] == 'Y'),
				);
			}
			if (isset($arIBlock['FIELDS']['SECTION_CODE']['DEFAULT_VALUE']))
			{
				$arTransSettings = $arIBlock['FIELDS']['SECTION_CODE']['DEFAULT_VALUE'];
				$boolTranslitSection = ($arTransSettings['TRANSLITERATION'] == 'Y');
				$arTranslitSection = array(
					"max_len" => $arTransSettings['TRANS_LEN'],
					"change_case" => $arTransSettings['TRANS_CASE'],
					"replace_space" => $arTransSettings['TRANS_SPACE'],
					"replace_other" => $arTransSettings['TRANS_OTHER'],
					"delete_repeat_replace" => ($arTransSettings['TRANS_EAT'] == 'Y'),
					"use_google" => ($arTransSettings['USE_GOOGLE'] == 'Y'),
				);
			}
		}

		// Prepare load arrays
		$strAvailGroupFields = COption::GetOptionString("catalog", "allowed_group_fields", $defCatalogAvailGroupFields);
		$arAvailGroupFields = explode(",", $strAvailGroupFields);
		$arAvailGroupFields_names = array();
		for ($i = 0, $intCount = count($arAvailGroupFields), $intCount2 = count($arCatalogAvailGroupFields); $i < $intCount; $i++)
		{
			for ($j = 0; $j < $intCount2; $j++)
			{
				if ($arCatalogAvailGroupFields[$j]["value"]==$arAvailGroupFields[$i])
				{
					$arAvailGroupFields_names[$arAvailGroupFields[$i]] = array(
						"field" => $arCatalogAvailGroupFields[$j]["field"],
						"important" => $arCatalogAvailGroupFields[$j]["important"]
						);
					break;
				}
			}
		}

		// Prepare load arrays
		$strAvailProdFields = COption::GetOptionString("catalog", "allowed_product_fields", $defCatalogAvailProdFields);
		$arAvailProdFields = explode(",", $strAvailProdFields);
		$arAvailProdFields_names = array();
		for ($i = 0, $intCount = count($arAvailProdFields), $intCount2 = count($arCatalogAvailProdFields); $i < $intCount; $i++)
		{
			for ($j = 0; $j < $intCount2; $j++)
			{
				if ($arCatalogAvailProdFields[$j]["value"]==$arAvailProdFields[$i])
				{
					$arAvailProdFields_names[$arAvailProdFields[$i]] = array(
						"field" => $arCatalogAvailProdFields[$j]["field"],
						"important" => $arCatalogAvailProdFields[$j]["important"]
						);
					break;
				}
			}
		}

		// Prepare load arrays
		$strAvailPriceFields = COption::GetOptionString("catalog", "allowed_product_fields", $defCatalogAvailPriceFields);
		$arAvailPriceFields = explode(",", $strAvailPriceFields);
		$arAvailPriceFields_names = array();
		for ($i = 0, $intCount = count($arAvailPriceFields), $intCount2 = count($arCatalogAvailPriceFields); $i < $intCount; $i++)
		{
			if ($boolUseStoreControl && array_key_exists($arAvailPriceFields[$i], $arDisableFields))
				continue;

			for ($j = 0; $j < $intCount2; $j++)
			{
				if ($arCatalogAvailPriceFields[$j]["value"]==$arAvailPriceFields[$i])
				{
					$arAvailPriceFields_names[$arAvailPriceFields[$i]] = array(
						"field" => $arCatalogAvailPriceFields[$j]["field"],
						"important" => $arCatalogAvailPriceFields[$j]["important"]
					);
					break;
				}
			}
		}

		// Prepare load arrays
		$strAvailValueFields = COption::GetOptionString("catalog", "allowed_price_fields", $defCatalogAvailValueFields);
		$arAvailValueFields = explode(",", $strAvailValueFields);
		$arAvailValueFields_names = array();
		for ($i = 0, $intCount = count($arAvailValueFields), $intCount2 = count($arCatalogAvailValueFields); $i < $intCount; $i++)
		{
			for ($j = 0; $j < $intCount2; $j++)
			{
				if ($arCatalogAvailValueFields[$j]["value"] == $arAvailValueFields[$i])
				{
					$arAvailValueFields_names[$arAvailValueFields[$i]] = array(
						"field_name_size" =>  $arCatalogAvailValueFields[$j]["value_size"],
						"field" => $arCatalogAvailValueFields[$j]["field"],
						"important" => $arCatalogAvailValueFields[$j]["important"]
					);
					break;
				}
			}
		}

		$previousProductId = false;
		$updateFacet = true;
		$newProducts = array();
		CIBlock::disableClearTagCache();
		// main
		do
		{
			// обновление старой цены
			$arFilter = array( 'IBLOCK_ID'=>$IBLOCK_ID, 'PROPERTY_ARTNUMBER' => trim($arRes[10]) );
			$res = CIBlockElement::GetList(
				array(),
				$arFilter,
				false,
				false,
				array('ID', 'PREVIEW_PICTURE', 'DETAIL_PICTURE', 'IBLOCK_SECTION_ID', 'PROPERTY_ARTNUMBER')
			);
			if ($arr = $res->Fetch()) {
				$saleprice = (float)preg_replace('/[^0-9]+/', '', $arRes[12]);
				if( !$saleprice || ($saleprice < 0)) {
					$saleprice = '';
				}
				
				CIBlockElement::SetPropertyValuesEx(
					$arr['ID'], 
					$IBLOCK_ID, 
					array(105=>$saleprice) // SALEPRICE
				);
				
				$price = (float)preg_replace('/[^0-9]+/', '', $arRes[11]);
				if ($price > 0) {
					CPrice::SetBasePrice($arr['ID'], $price, 'RUB');
				}
			}
		}
		while ($arRes = $csvFile->Fetch());
	}
}

if ($bTmpUserCreated)
{
	if (isset($USER_TMP))
	{
		$USER = $USER_TMP;
		unset($USER_TMP);
	}
}
