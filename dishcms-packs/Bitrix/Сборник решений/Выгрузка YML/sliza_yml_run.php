<?php
//<title>Yandex</title>
/** @global CUser $USER */
/** @global CMain $APPLICATION */
use Bitrix\Currency,
	Bitrix\Iblock,
	Bitrix\Catalog;

IncludeModuleLangFile($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/catalog/export_yandex.php');
set_time_limit(0);

$arRunErrors=array();
$usedProtocol = (isset($USE_HTTPS) && $USE_HTTPS == 'Y' ? 'https://' : 'http://');

if (!\Bitrix\Main\Loader::includeModule('iblock')) {
    $arRunErrors[]='iblock module not loaded';
}
if (!\Bitrix\Main\Loader::includeModule('catalog')) {
    $arRunErrors[]='catalog module not loaded';
}

/**
 * functions -----------------------------------------------------------------------------
 */
$__fpContent='';
$fHSC=function($text, $bHSC=true) {
    return $bHSC ? htmlspecialcharsbx($text) : $text;
};
$fC=function($text, $bHSC=true, $bConvert=true) use ($fHSC) {
    if(!$bConvert) return $text;
    global $APPLICATION;
    return $APPLICATION->ConvertCharset($fHSC($text, $bHSC), LANG_CHARSET, 'windows-1251');
};
$fRp=function($arg) {
    if (in_array($arg[0], array('&quot;', '&amp;', '&lt;', '&gt;'))) return $arg[0];
    else return ' ';
};
$fXml=function($text, $bHSC=false, $bDblQuote = false) use ($fC) {
    $bHSC = (true == $bHSC ? true : false);
    $bDblQuote = (true == $bDblQuote ? true: false);   
    if ($bHSC) {
        $text = htmlspecialcharsbx($text);
        if ($bDblQuote)
            $text = str_replace('&quot;', '"', $text);
    }
    $text = preg_replace("/[\x1-\x8\xB-\xC\xE-\x1F]/", "", $text);
    $text = str_replace("'", "&apos;", $text);
    $text = $fC($text, false);
    return $text;    
};
$fW=function($content, $prepend=false) use (&$__fpContent) { //, $arRunErrors) {
    //if (empty($arRunErrors)) {
        if($prepend) $__fpContent=$content . $__fpContent;
        else $__fpContent.=$content;
		return true;
    //}
	return false;
};
$fAttrs=function($attrs=array(), $bConvert=true, $bHSC=true) use ($fC, $fHSC) {
    if(!empty($attrs)) {
		$s=array();
        foreach($attrs as $name=>$v) $s[]=$name.'="'.$fC($v, $bHSC, $bConvert).'"';
        return ' '.implode(' ', $s);
    }
    return '';
};
$fTagO=function($tag, $attrs=array(), $newLine=true, $close=false) use ($fW, $fAttrs) {
    $fW("<{$tag}".$fAttrs($attrs));
    if($close) $fW('/');
    $fW('>');
    if($newLine) $fW("\n");
};
$fTagC=function($tag) use ($fW) {
    $fW("</{$tag}>\n");
};
$fTag=function($tag, $content, $attrs=array(), $bConvert=true, $bHSC=true) use ($fW, $fC, $fHSC, $fTagO, $fTagC) {
    if($bConvert) $content=$fC($content, $bHSC);
    elseif($bHSC) $content=$fHSC($content);
    $fTagO($tag, $attrs, false);
    $fW($content);
    $fTagC($tag);
};

$fGetMinPrice=function($PRODUCT_ID) use ($XML_DATA, $ar_iblock) {
    $minPrice = 0;
    if ($XML_DATA['PRICE'] > 0) {
        $rsPrices = CPrice::GetListEx(array(),array(
            'PRODUCT_ID' => $PRODUCT_ID,
            'CATALOG_GROUP_ID' => $XML_DATA['PRICE'],
            'CAN_BUY' => 'Y',
            'GROUP_GROUP_ID' => array(5),
            '+<=QUANTITY_FROM' => 1,
            '+>=QUANTITY_TO' => 1,
            )
        );
        if ($arPrice = $rsPrices->Fetch()) {
            if ($arOptimalPrice = CCatalogProduct::GetOptimalPrice(
                $PRODUCT_ID,
                1,
                array(5), 
                'N',
                array($arPrice),
                $ar_iblock['LID'],
                array()
            ))
            {
                $minPrice = $arOptimalPrice['RESULT_PRICE']['DISCOUNT_PRICE'];
            }
        }
    }
    else
    {
        if ($arPrice = CCatalogProduct::GetOptimalPrice(
            $PRODUCT_ID,
            1,
            array(5), 
            'N',
            array(),
            $ar_iblock['LID'],
            array()
        ))
        {
            $minPrice = $arPrice['RESULT_PRICE']['DISCOUNT_PRICE'];
        }
    }
    return $minPrice;
};
// -----------------------------------------------------------------------------

global $USER, $APPLICATION;
$bTmpUserCreated = false;
if (!CCatalog::IsUserExists())
{
	$bTmpUserCreated = true;
	if (isset($USER))
	{
		$USER_TMP = $USER;
		unset($USER);
	}

	$USER = new CUser();
}

CCatalogDiscountSave::Disable();
CCatalogDiscountCoupon::ClearCoupon();
if ($USER->IsAuthorized())
	CCatalogDiscountCoupon::ClearCouponsByManage($USER->GetID());

if (strlen($SETUP_FILE_NAME) <= 0)
{
	$arRunErrors[] = GetMessage("CATI_NO_SAVE_FILE");
}
elseif (preg_match(BX_CATALOG_FILENAME_REG,$SETUP_FILE_NAME))
{
	$arRunErrors[] = GetMessage("CES_ERROR_BAD_EXPORT_FILENAME");
}
else
{
	$SETUP_FILE_NAME = Rel2Abs("/", $SETUP_FILE_NAME);
}

if ($XML_DATA && CheckSerializedData($XML_DATA))
{
	$XML_DATA = unserialize(stripslashes($XML_DATA));
	if (!is_array($XML_DATA)) $XML_DATA = array();
}

$IBLOCK_ID = (int)$IBLOCK_ID;
$db_iblock = CIBlock::GetByID($IBLOCK_ID);
if (!($ar_iblock = $db_iblock->Fetch())) {
	$arRunErrors[] = str_replace('#ID#', $IBLOCK_ID, GetMessage('YANDEX_ERR_NO_IBLOCK_FOUND_EXT'));
}
else {
	$SETUP_SERVER_NAME = trim($SETUP_SERVER_NAME);
	if (strlen($SETUP_SERVER_NAME) <= 0) 	{
		if (strlen($ar_iblock['SERVER_NAME']) <= 0) {
			$b = "sort";
			$o = "asc";
			$rsSite = CSite::GetList($b, $o, array("LID" => $ar_iblock["LID"]));
			if($arSite = $rsSite->Fetch())
				$ar_iblock["SERVER_NAME"] = $arSite["SERVER_NAME"];
			if(strlen($ar_iblock["SERVER_NAME"])<=0 && defined("SITE_SERVER_NAME"))
				$ar_iblock["SERVER_NAME"] = SITE_SERVER_NAME;
			if(strlen($ar_iblock["SERVER_NAME"])<=0)
				$ar_iblock["SERVER_NAME"] = COption::GetOptionString("main", "server_name", "");
		}
	}
	else {
		$ar_iblock['SERVER_NAME'] = $SETUP_SERVER_NAME;
	}
	$ar_iblock['PROPERTY'] = array();
	$rsProps = CIBlockProperty::GetList(
		array('SORT' => 'ASC', 'NAME' => 'ASC'),
		array('IBLOCK_ID' => $IBLOCK_ID, 'ACTIVE' => 'Y', 'CHECK_PERMISSIONS' => 'N')
	);
	while ($arProp = $rsProps->Fetch())
	{
		$arProp['ID'] = (int)$arProp['ID'];
		$arProp['USER_TYPE'] = (string)$arProp['USER_TYPE'];
		$arProp['CODE'] = (string)$arProp['CODE'];
		$ar_iblock['PROPERTY'][$arProp['ID']] = $arProp;
	}
}

global $iblockServerName;
$iblockServerName = $ar_iblock["SERVER_NAME"];

$arProperties = array();
if (isset($ar_iblock['PROPERTY']))
	$arProperties = $ar_iblock['PROPERTY'];

// --- start xml ---
$fW('<?if (!isset($_GET["referer1"]) || strlen($_GET["referer1"])<=0) $_GET["referer1"] = "yandext";?>');
$fW('<? $strReferer1 = htmlspecialchars($_GET["referer1"]); ?>');
$fW('<?if (!isset($_GET["referer2"]) || strlen($_GET["referer2"]) <= 0) $_GET["referer2"] = "";?>');
$fW('<? $strReferer2 = htmlspecialchars($_GET["referer2"]); ?>');
$fW('<? header("Content-Type: text/xml; charset=windows-1251");?>');
$fW('<? echo "<"."?xml version=\"1.0\" encoding=\"windows-1251\"?".">"?>');
$fW("\n".'<!DOCTYPE yml_catalog SYSTEM "shops.dtd">'."\n");
$fTagO('yml_catalog', array('date'=>date("Y-m-d H:i")));
$fTagO('shop');
    $fTag('name', 'Ocsi Style');
    $fTag('company', 'ИП Статникова');
    $fTag('url', $usedProtocol.$ar_iblock['SERVER_NAME']);
    $fTag('platform', '1C-Bitrix');

if(empty($arRunErrors)) { // currencies (толька для удобства свертки в редакторе)
    $fTagO('currencies');
        $RUR = 'RUB';
        $currencyIterator = Currency\CurrencyTable::getList(array(
            'select' => array('CURRENCY'),
            'filter' => array('=CURRENCY' => 'RUR')
        ));
        if($currency = $currencyIterator->fetch())
            $RUR = 'RUR';
        unset($currency, $currencyIterator);
        
        $arCurrencyAllowed = array($RUR, 'USD', 'EUR', 'UAH', 'BYR', 'KZT');

        $BASE_CURRENCY = Currency\CurrencyManager::getBaseCurrency();
        if(is_array($XML_DATA['CURRENCY'])) {
            foreach($XML_DATA['CURRENCY'] as $CURRENCY => $arCurData) {
                if(in_array($CURRENCY, $arCurrencyAllowed)) {
                    $attrs=array(
                        'id'=>$CURRENCY, 
                        'rate'=>($arCurData['rate'] == 'SITE' ? CCurrencyRates::ConvertCurrency(1, $CURRENCY, $RUR) : $arCurData['rate'])
                    );
                    if($arCurData['plus'] > 0) $attrs['plus']=(int)$arCurData['plus'];
                    $fTagO('currency', $attrs, true, true);
                }
			}
            unset($CURRENCY, $arCurData);
        }
        else {
            $currencyIterator = Currency\CurrencyTable::getList(array(
                'select' => array('CURRENCY', 'SORT'),
                'filter' => array('@CURRENCY' => $arCurrencyAllowed),
                'order' => array('SORT' => 'ASC', 'CURRENCY' => 'ASC')
            ));
            while ($currency = $currencyIterator->fetch()) {
                $fTagO('currency', array(
                    'id'=>$currency['CURRENCY'],
                    'rate'=>(CCurrencyRates::ConvertCurrency(1, $currency['CURRENCY'], $RUR))
                ), true, true);
            }
            unset($currency, $currencyIterator);
        }
    $fTagC('currencies');
}

$arSectionsID=array();
if(empty($arRunErrors)) { // categories
    $rsSection = CIBlockSection::GetTreeList(
        array('IBLOCK_ID'=>$IBLOCK_ID, 'ACTIVE'=>'Y', 'GLOBAL_ACTIVE'=>'Y'),
        array('ID', 'IBLOCK_SECTION_ID', 'NAME', 'DEPTH_LEVEL', 'LEFT_MARGIN')
    );
    
    $fTagO('categories');
    while($arSection = $rsSection->GetNext()) {
		$arSectionsID[]=$arSection['ID'];
        $attrs=array('id'=>$arSection['ID']);
        if(!empty($arSection['IBLOCK_SECTION_ID'])) $attrs['parentId']=$arSection['IBLOCK_SECTION_ID'];
        $fTag('category', $arSection['NAME'], $attrs);
    }    
    $fTagC('categories');
}
   
if(empty($arRunErrors)) { // offers
    $fTagO('offers');
    
    $rsProducts=CIBlockElement::GetList(
        array('IBLOCK_SECTION_ID'=>'ASC', 'NAME'=>'ASC'),
        array('IBLOCK_ID'=>$IBLOCK_ID, 'ACTIVE'=>'Y', 'SECTION_ID'=>$arSectionsID),
        false, 
        false,
        array('IBLOCK_ID', 'ID', 'NAME', 'PREVIEW_PICTURE', 'DETAIL_PICTURE', 'DETAIL_PAGE_URL')
    );
    $arProducts=array();
    $arProductsProps=array();
    $arProductIDs=array();
    while($obProduct=$rsProducts->GetNextElement()) {
		$arProduct=$obProduct->GetFields();
        $arProducts[$arProduct['ID']]=$arProduct;
        $arProductsProps[$arProduct['ID']]=$obProduct->GetProperties();
        $arProductIDs[]=$arProduct['ID'];
    }
    
    if(!empty($arProducts)) 
    {
        // @var callable $fPictureTag
        $fPictureTag=function($fileId, $tagName='picture') use ($fTag, $usedProtocol, $iblockServerName) {
            if(!empty($fileId)) {
                $src=KonturGetImageSrc($fileId);
                if(!empty($src)) {
                    if(substr($src, 0, 1) == "/") {
                        $fTag($tagName, $usedProtocol.$iblockServerName.CHTTP::urnEncode($src, 'utf-8'));
                    }
                    else {
                        $fTag($tagName, $src);
                    }
                }
            }
        };
        // @var callable $fOfferTag
        $fOfferTag=function($arOffer, $arProps=array(), $isOffer=false, $arProduct=null, $arProductProps=null) 
            use ($ar_iblock, $XML_DATA, $usedProtocol, $iblockServerName, $fHSC, $fTagO, $fTag, $fTagC, $fPictureTag, $fGetMinPrice)
        {
            // @var callable $fGetProp
            $fGetProp=function($propCode, $default=null, $ifEmptyUseProduct=true, $primaryProduct=false, $key='VALUE') 
                use ($arProps, $arProductProps, $isOffer) 
            {
                if($primaryProduct) {
                    if(!empty($arProductProps[$propCode][$key])) return $arProductProps[$propCode][$key];
                    elseif(!empty($arProps[$propCode][$key])) return $arProps[$propCode][$key];
                    return $default;
                }
                if(!empty($arProps[$propCode][$key])) {
                    return $arProps[$propCode][$key];
                }
                if($isOffer && !empty($arProductProps[$propCode][$key]) && $ifEmptyUseProduct) {
                    return $arProductProps[$propCode][$key];
                }
                return $default;
            };
            // @var callable $fGetItemProp
            $fGetItemProp=function($propCode, $default=null, $primaryProduct=false, $ifEmptyUseProduct=true) 
                use ($arOffer, $arProduct, $isOffer) 
            {
                if($primaryProduct) {
                    if(!empty($arProduct[$propCode])) return $arProduct[$propCode];
                    elseif(!empty($arOffer[$propCode])) return $arOffer[$propCode];
                    return $default;
                }
                if(!empty($arOffer[$propCode])) return $arOffer[$propCode];
                elseif($isOffer && !empty($arProduct[$propCode]) && $ifEmptyUseProduct) {
                    return $arProduct[$propCode];
                }
                return $default;
            };
            // @var callable $fParamTags
            $fParamTags=function($arParamNames) use ($fGetProp, $fTag) {
                if(!is_array($arParamNames)) $arParamNames=array($arParamNames);
                foreach($arParamNames as $arParamName) {
                    $name=$fGetProp($arParamName, null, true, false, 'NAME');
                    $value=$fGetProp($arParamName);
                    if(!empty($name) && !empty($value)) {
                        if(is_array($value)) {
                            // foreach($value as $v) $fTag('param', $v, array('name'=>$name));
                            $fTag('param', implode('; ', $value), array('name'=>$name));
                        }
                        else {
                            $fTag('param', $value, array('name'=>$name));
                        }
                    }
                }
            };
            
            $fTagO('offer', array('id'=>$arOffer['ID']));
                // $fTag('id', $arOffer['ID']);
                
                $SIZE=trim($fGetProp('SIZES_CLOTHES', ''));
                $NAME_POSTFIX=$SIZE ? " (размер {$SIZE})" : '';
                $DESCRIPTION_POSTFIX=$SIZE ? ", Размер {$SIZE}." : '';
                
                $fTag('name', $fGetItemProp('NAME', '', true) . $NAME_POSTFIX);
                
                $url=$fHSC($usedProtocol.$iblockServerName.$arOffer['DETAIL_PAGE_URL']);
                /* $url.=((strstr($url, '?') === false) ? '?' : '&amp;') . 'r1=<?echo $strReferer1; ?>&amp;r2=<?echo $strReferer2; ?>'; */
                $fTag('url', $url, array(), false, false);
                try {
                $fTag('price', $fGetMinPrice($arOffer['ID']));
                }
                catch(Exception $e) {
                    var_dump($fGetMinPrice, $fGetMinPrice($arOffer['ID'])); die;
                }
                $fTag('currencyId', 'RUB');
                $fTag('categoryId', $fGetItemProp('IBLOCK_SECTION_ID', null, true));
                
                $DETAIL_PICTURE = (int)$fGetItemProp('DETAIL_PICTURE', 0, true);
                $PREVIEW_PICTURE = (int)$fGetItemProp('PREVIEW_PICTURE', 0, true);
                $fPictureTag($DETAIL_PICTURE > 0 ? $DETAIL_PICTURE : $PREVIEW_PICTURE);
                
                $arMorePhotos=array_slice($fGetProp('MORE_PHOTO', array()), 0, 2);
                foreach($arMorePhotos as $photoId) {
                    $fPictureTag($photoId);
                }
                
   				// получение мета-тэгов
                // if($isOffer) $meta=$arProduct;
				//else $meta=$arOffer;
                //$ipropValues=new Iblock\InheritedProperty\ElementValues($meta['IBLOCK_ID'], $meta['ID']);
                //$ipvValues=$ipropValues->getValues();
                //$fTag('description', trim($ipvValues['ELEMENT_META_TITLE'] . $SIZES_CLOTHES));
                
                $fTag('description', implode('; ', $fGetProp('SOSTAV')) . $DESCRIPTION_POSTFIX);
                
                $fParamTags(array(
                    'BRAND_REF', 'ARTNUMBER', 'MANUFACTURER', 'COLOR_REF', 'SIZES_SHOES',
                    'SIZES_CLOTHES', 'SOSTAV', 'SEZON', 'KOLLEKZIA', 'WEIGHT',
                    'MODIFY_REF', 'SM_SIZE', 'GUARANTEE'
                ));

            $fTagC('offer');
        };
        
        $arOffersExists=CCatalogSKU::getExistOffers($arProductIDs);
        foreach($arProducts as $id=>$arProduct) { 
            if($arOffersExists[$id]) {
                $arAllOffers=CCatalogSKU::getOffersList($id); 
				if(!empty($arAllOffers)) {
                	foreach($arAllOffers as $id=>$arOffers) {
						foreach($arOffers as $arOffer) {
	                		$rs=CIBlockElement::GetList(array('ID'=>'ASC'),array('IBLOCK_ID'=>$arOffer['IBLOCK_ID'], 'ID'=>$arOffer['ID'], 'ACTIVE'=>'Y'));
    	            		if($offer=$rs->GetNextElement()) {
								$fOfferTag(
                                    $offer->GetFields(), 
                                    $offer->GetProperties(),
                                    true, 
                                    $arProduct,
                                    $arProductsProps[$id]
                                );
	        	            }
						}
                    }
                }
            }
            else {				
                $fOfferTag($arProduct, $arProductsProps[$id]);
            }
        } 
    }
    
    $fTagC('offers');
}
$fTagC('shop');
$fTagC('yml_catalog');

// --- end xml ---    
// end ------------------------------

// write output file    
if (empty($arRunErrors))
{
	CheckDirPath($_SERVER["DOCUMENT_ROOT"].$SETUP_FILE_NAME);

	if (!$fp = @fopen($_SERVER["DOCUMENT_ROOT"].$SETUP_FILE_NAME, "wb"))
	{
		$arRunErrors[] = str_replace('#FILE#', $_SERVER["DOCUMENT_ROOT"].$SETUP_FILE_NAME, GetMessage('YANDEX_ERR_FILE_OPEN_WRITING'));
	}
	else {
		if (!fwrite($fp, $__fpContent)) 
		{
       		$arRunErrors[] = str_replace('#FILE#', $_SERVER["DOCUMENT_ROOT"].$SETUP_FILE_NAME, GetMessage('YANDEX_ERR_SETUP_FILE_WRITE'));
        }
        fclose($fp);
	}
}

CCatalogDiscountSave::Enable();

if (!empty($arRunErrors))
	$strExportErrorMessage = implode('<br />',$arRunErrors);

if ($bTmpUserCreated)
{
	unset($USER);
	if (isset($USER_TMP))
	{
		$USER = $USER_TMP;
		unset($USER_TMP);
	}
}
