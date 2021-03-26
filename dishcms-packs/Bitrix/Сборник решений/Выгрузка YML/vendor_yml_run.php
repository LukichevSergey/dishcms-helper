<?php
//<title>Yandex.Vendor</title>
/** @global CUser $USER */
/** @global CMain $APPLICATION */
use Bitrix\Main,
    Bitrix\Main\Loader,
    Bitrix\Currency,
    Bitrix\Iblock,
    Bitrix\Catalog,
    Bitrix\Sale;

IncludeModuleLangFile($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/catalog/export_yandex.php');
set_time_limit(0);

$arRunErrors=[];
$usedProtocol = (isset($USE_HTTPS) && $USE_HTTPS == 'Y' ? 'https://' : 'http://');

if (!\Bitrix\Main\Loader::includeModule('iblock')) {
    $arRunErrors[]='iblock module not loaded';
}
if (!\Bitrix\Main\Loader::includeModule('catalog')) {
    $arRunErrors[]='catalog module not loaded';
}

$SETUP_SERVER_NAME = (isset($SETUP_SERVER_NAME) ? trim($SETUP_SERVER_NAME) : '');
$COMPANY_NAME = (isset($COMPANY_NAME) ? trim($COMPANY_NAME) : '');
$SITE_ID = (isset($SITE_ID) ? (string)$SITE_ID : '');
if ($SITE_ID === '')
    $SITE_ID = $ar_iblock['LID'];
$iterator = Main\SiteTable::getList(array(
    'select' => array('LID', 'SERVER_NAME', 'SITE_NAME', 'DIR'),
    'filter' => array('=LID' => $SITE_ID, '=ACTIVE' => 'Y')
));
$site = $iterator->fetch();
unset($iterator);
if (empty($site))
{
    $arRunErrors[] = GetMessage('BX_CATALOG_EXPORT_YANDEX_ERR_BAD_SITE');
}
else
{
    $site['SITE_NAME'] = (string)$site['SITE_NAME'];
    if ($site['SITE_NAME'] === '')
        $site['SITE_NAME'] = (string)Main\Config\Option::get('main', 'site_name');
    $site['COMPANY_NAME'] = $COMPANY_NAME;
    if ($site['COMPANY_NAME'] === '')
        $site['COMPANY_NAME'] = (string)Main\Config\Option::get('main', 'site_name');
    $site['SERVER_NAME'] = (string)$site['SERVER_NAME'];
    if ($SETUP_SERVER_NAME !== '')
        $site['SERVER_NAME'] = $SETUP_SERVER_NAME;
    if ($site['SERVER_NAME'] === '')
    {
        $site['SERVER_NAME'] = (defined('SITE_SERVER_NAME')
            ? SITE_SERVER_NAME
            : (string)Main\Config\Option::get('main', 'server_name')
        );
    }
    if ($site['SERVER_NAME'] === '')
    {
        $arRunErrors[] = GetMessage('BX_CATALOG_EXPORT_YANDEX_ERR_BAD_SERVER_NAME');
    }
}

// -----------------------------------------------------------------------------

class YmlGenerator
{
    private $xml='';

    public function convert($text) {
        global $APPLICATION;
        return $this->useConvert ? $APPLICATION->ConvertCharset($text, LANG_CHARSET, $this->convertCharset) : $text;
    }

    public function hsc($text, $hsc=true) {
        return $hsc ? htmlspecialcharsbx($text) : $text;
    }

    public function getXml($convert=false, $charset='windows-1251')
    {
        if(!$convert) $charset=LANG_CHARSET;
        $xml='<?if (!isset($_GET["referer1"]) || strlen($_GET["referer1"])<=0) $_GET["referer1"] = "yandext";?>';
        $xml.='<? $strReferer1 = htmlspecialchars($_GET["referer1"]); ?>';
        $xml.='<?if (!isset($_GET["referer2"]) || strlen($_GET["referer2"]) <= 0) $_GET["referer2"] = "";?>';
        $xml.='<? $strReferer2 = htmlspecialchars($_GET["referer2"]); ?>';
        $xml.='<? header("Content-Type: text/xml; charset='.$charset.'");?>';
        $xml.='<? echo "<"."?xml version=\"1.0\" encoding=\"'.$charset.'\"?".">"?>';
        $xml.="\n".'<!DOCTYPE yml_catalog SYSTEM "shops.dtd">'."\n";
        $xml.=$this->xml;

        return $convert ? $this->convert($xml) : $xml;
    }

    public function normalizeXml($text, $bHSC=false, $bDblQuote = false) {
        $bHSC = (true === $bHSC ? true : false);
        $bDblQuote = (true === $bDblQuote ? true: false);   
        if ($bHSC) {
            $text = htmlspecialcharsbx($text);
            if ($bDblQuote)
                $text = str_replace('&quot;', '"', $text);
        }
        $text = preg_replace("/[\x1-\x8\xB-\xC\xE-\x1F]/", "", $text);
        $text = str_replace("'", "&apos;", $text);
        $text = $this->convert($text);
        return $text;    
    }

    public function write($content, $prepend=false) {
        if($prepend) $this->xml=$content . $this->xml;
        else $this->xml.=$content;
    }

    public function openTag($tag, $attributes=[], $newLine=true, $close=false) {
        array_walk($attributes, function(&$v, $k) { $v="{$k}=\"{$v}\""; });
        $this->write('<' . trim("{$tag} " . implode(' ', $attributes)));
        if($close) $this->write('/');
        $this->write('>');
        if($newLine) $this->write("\n");
    }

    public function closeTag($tag) {
        $this->write("</{$tag}>\n");
    }

    public function tag($tag, $content, $attrs=array(), $hsc=true) {
        if($hsc) $content=$this->hsc($content);
        $this->openTag($tag, $attrs, false);
        $this->write($content);
        $this->closeTag($tag);
    }

    public function getSectionPath($iblockId, $sectionId)
    {
        $sections=[];
        $rs=CIBlockSection::GetNavChain($iblockId, $sectionId, ['ID', 'DEPTH_LEVEL', 'NAME']);
        while($section=$rs->Fetch()) {
            $sections[(int)$section['DEPTH_LEVEL']]=$section;
        }
        return $sections;
    }

    public function getRootSectionName($iblockId, $sectionId) {
        return $this->getSectionPath($iblockId, $sectionId)[1]['NAME']??'';
    }

    public function stripSpecialChars($text) {
        // @fixme ???
        if (in_array($arg[0], array('&quot;', '&amp;', '&lt;', '&gt;'))) return $arg[0];
        else return ' ';
    }
}

$yml=new YmlGenerator();

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
    if (strlen($SETUP_SERVER_NAME) <= 0)    {
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
$yml->openTag('yml_catalog', ['date'=>date("Y-m-d H:i")]);
$yml->openTag('shop');
$yml->tag('name', $site['COMPANY_NAME']);
$yml->tag('company', $site['COMPANY_NAME']);
$yml->tag('url', $usedProtocol.$ar_iblock['SERVER_NAME']);
$yml->tag('platform', '1C-Bitrix');

if(empty($arRunErrors)) { // currencies (толька для удобства свертки в редакторе)
    $yml->openTag('currencies');
    $RUR = 'RUB';
    $currencyIterator = Currency\CurrencyTable::getList(array(
        'select' => array('CURRENCY'),
        'filter' => array('=CURRENCY' => 'RUR')
    ));
    if($currency = $currencyIterator->fetch()) $RUR = 'RUR';
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
                    $yml->openTag('currency', $attrs, true, true);
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
                $yml->openTag('currency', array(
                    'id'=>$currency['CURRENCY'],
                    'rate'=>(CCurrencyRates::ConvertCurrency(1, $currency['CURRENCY'], $RUR))
                ), true, true);
            }
            unset($currency, $currencyIterator);
        }

    $yml->closeTag('currencies');
}

$arSectionsID=array();
if(empty($arRunErrors)) { // categories
    $rsSection = CIBlockSection::GetTreeList(
        array('IBLOCK_ID'=>$IBLOCK_ID, 'ACTIVE'=>'Y', 'GLOBAL_ACTIVE'=>'Y'),
        array('ID', 'IBLOCK_SECTION_ID', 'NAME', 'DEPTH_LEVEL', 'LEFT_MARGIN')
    );
    
    $yml->openTag('categories');
    $rootDepthLevel=null;
    while($arSection = $rsSection->GetNext()) {
        if(in_array($arSection['ID'], $V)) {
            $arSectionsID[]=$arSection['ID'];
            $rootDepthLevel=$arSection['DEPTH_LEVEL'];
            $attrs=array('id'=>$arSection['ID']);
            $yml->tag('category', $arSection['NAME'], $attrs);
        }
        elseif($rootDepthLevel && ($arSection['DEPTH_LEVEL'] > $rootDepthLevel)) {
            $arSectionsID[]=$arSection['ID'];
            $attrs=array('id'=>$arSection['ID']);
            if(!empty($arSection['IBLOCK_SECTION_ID'])) $attrs['parentId']=$arSection['IBLOCK_SECTION_ID'];
            $yml->tag('category', $arSection['NAME'], $attrs);
        }
        else {
            $rootDepthLevel=null;
        }        
    }    
    $yml->closeTag('categories');
}
   
if(empty($arRunErrors)) { // offers
    $yml->openTag('offers');
    
    $vendorProp='PROPERTY_9.NAME';
    /** @var []  массив <param> вида [name=>property_id] */
    $offerParams=[
        "Класс"=>34,
        "Тип калибровки"=>36,
        "Единицы измерения"=>37,
        "Мин. предел взвешивания"=>35,
        "Макс. предел взвешивания"=>38
    ];
    $rsProducts=CIBlockElement::GetList(
        array('IBLOCK_SECTION_ID'=>'ASC', 'NAME'=>'ASC'),
        array('IBLOCK_ID'=>$IBLOCK_ID, 'ACTIVE'=>'Y', 'SECTION_ID'=>$arSectionsID),
        false, 
        false,
        array_merge(
            array('IBLOCK_ID', 'ID', 'IBLOCK_SECTION_ID', 'NAME', 'PREVIEW_PICTURE', 'DETAIL_PICTURE', 'DETAIL_PAGE_URL', $vendorProp),
            array_map(function($id) { return 'PROPERTY_' . $id; }, $offerParams)
        )
    );

    $arProducts=array();
    while($obProduct=$rsProducts->GetNextElement()) {
        $arProduct=$obProduct->GetFields();
        $arProducts[$arProduct['ID']]=$arProduct;
    }

    if(!empty($arProducts)) {
        $fOfferTag=function($offer) use (&$yml, $offerParams, $ar_iblock, $XML_DATA, $usedProtocol, $iblockServerName)
        {
            $minPrice = 0;
            if ($XML_DATA['PRICE'] > 0) {
                $rsPrices = CPrice::GetListEx(array(),array(
                    'PRODUCT_ID' => $offer['ID'],
                    'CATALOG_GROUP_ID' => $XML_DATA['PRICE'],
                    'CAN_BUY' => 'Y',
                    'GROUP_GROUP_ID' => array(2),
                    '+<=QUANTITY_FROM' => 1,
                    '+>=QUANTITY_TO' => 1,
                    )
                );
                if ($arPrice = $rsPrices->Fetch()) {
                    if ($arOptimalPrice = CCatalogProduct::GetOptimalPrice(
                        $offer['ID'],
                        1,
                        array(2), // anonymous
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
                    $offer['ID'],
                    1,
                    array(2), // anonymous
                    'N',
                    array(),
                    $ar_iblock['LID'],
                    array()
                ))
                {
                    $minPrice = $arPrice['RESULT_PRICE']['DISCOUNT_PRICE'];
                }
            }        

            $yml->openTag('offer', ['id'=>$offer['ID'], 'type'=>'vendor.model']);
            if($rootSectionName=$yml->getRootSectionName($offer['IBLOCK_ID'], $offer['IBLOCK_SECTION_ID'])) {
                $yml->tag('typePrefix', $rootSectionName);

                $rootSectionNameWords=explode(' ' , preg_replace('/\s+?/U', ' ', preg_quote(trim($rootSectionName))));
                array_walk($rootSectionNameWords, function(&$word) {
                	$word='(' . mb_strtoupper(mb_substr($word, 0, 1)) . '|' . mb_strtolower(mb_substr($word, 0, 1)) . ')' . mb_substr($word, 1);
               	});
                $offer['NAME']=preg_replace('/('.implode('|', $rootSectionNameWords).')/i', '', $offer['NAME']);
            }

            if(isset($offer['PROPERTY_9_NAME'])) {
                $yml->tag('vendor', $offer['PROPERTY_9_NAME']);
            }

            $yml->tag('model', trim($offer['NAME']));

            $url=$usedProtocol.$iblockServerName.$arOffer['DETAIL_PAGE_URL'];
            $url.=((strstr($url, '?') === false) ? '?' : '&amp;') . 'r1=<?echo $strReferer1; ?>&amp;r2=<?echo $strReferer2; ?>';
            $yml->tag('url', $url, [], false);

            $yml->tag('price', $minPrice);

            if(!empty($offer['PROPERTY_OLD_PRICE']['VALUE']) && ((int)$minPrice < (int)$offer['PROPERTY_OLD_PRICE']['VALUE'])) {
                $this->tag('oldprice', $offer['PROPERTY_OLD_PRICE']['VALUE']);
            }
            
            $yml->tag('currencyId', 'RUB');
            $yml->tag('categoryId', $offer['IBLOCK_SECTION_ID']);

                $DETAIL_PICTURE = (int)$offer["DETAIL_PICTURE"];
                $PREVIEW_PICTURE = (int)$offer["PREVIEW_PICTURE"];
                if($DETAIL_PICTURE > 0 || $PREVIEW_PICTURE > 0) {
                    $pictNo = ($DETAIL_PICTURE > 0 ? $DETAIL_PICTURE : $PREVIEW_PICTURE);
                    if ($ar_file = CFile::GetFileArray($pictNo)) {
                        if(substr($ar_file["SRC"], 0, 1) == "/") {
                            $strFile = $usedProtocol.$iblockServerName.CHTTP::urnEncode($ar_file["SRC"], 'utf-8');
                        }
                        else {
                            $strFile = $ar_file["SRC"];
                        }
                        $yml->tag('picture', $strFile);
                    }
                }

            // $yml->tag('local_delivery_cost', '0');
                
            if(trim($offer['PREVIEW_TEXT'])) {
                $yml->tag('description', trim($offer['PREVIEW_TEXT']));
            }
                  
            // $yml->tag('sales_notes', 'Необходима предоплата в размере 100%');
            foreach($offerParams as $name=>$propId) {
                if(strpos($propId, '.') !== false) $key='PROPERTY_' . str_replace('.', '_', $propId);
                else $key="PROPERTY_{$propId}_VALUE";
                if(array_key_exists($key, $offer) && ($offer[$key]!==null) && ($offer[$key]!=='')) {
                    $yml->tag('param', $offer[$key], ['name'=>$name]);
                }
            }

            $yml->closeTag('offer');
        };

        foreach($arProducts as $id=>$offer) { 
            $fOfferTag($offer);
        }
    }
    
    $yml->closeTag('offers');
}
$yml->closeTag('shop');
$yml->closeTag('yml_catalog');

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
        if (!fwrite($fp, $yml->getXml())) 
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
