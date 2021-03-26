<?php
namespace kontur\handlers;

class UpdateCatalogQuantityHandler
{
	static public $catalogIBlockId=null;
	static public $offerIBlockId=null;
	public static function init($catalogIBlockId, $offerIBlockId)
	{
		\AddEventHandler("iblock", "OnAfterIBlockElementAdd", ['\kontur\handlers\UpdateCatalogQuantityHandler', "updateProductQuantity"]);
		\AddEventHandler("iblock", "OnAfterIBlockElementUpdate", ['\kontur\handlers\UpdateCatalogQuantityHandler', "updateProductQuantity"]);
		static::$catalogIBlockId=$catalogIBlockId;
		static::$offerIBlockId=$offerIBlockId;
	}

	public static function log($data, $file='update.log')
	{
		// file_put_contents(dirname(__FILE__) . '/' . $file, var_export($data, true) . "\n\n", FILE_APPEND);
	}

	public static function updateProductQuantity(&$arFields)
	{
		global $DB;

		if (!(\CModule::IncludeModule('catalog') && \CModule::IncludeModule('iblock'))) {
			return;
		}

		if(!empty($arFields['ID']) && !empty($arFields['IBLOCK_ID']) && ($arFields['IBLOCK_ID'] == static::$catalogIBlockId)) {
			$id=$arFields['ID'];
			$product = \CCatalogProduct::GetByID($id);
			$quantity = $product['QUANTITY'];
			$hasOffers = false;
			
            $arFilter = array(
            	'IBLOCK_ID' => static::$offerIBlockId,
                "PROPERTY_CML2_LINK" => $id,
                "!ID" => $id,
            );

            $obOffersList = \CIBlockElement::GetList(array("SORT"=>"ASC"), $arFilter, false, false, array("CATALOG_QUANTITY"));
            while ($arOffers = $obOffersList->Fetch()) {
            	if(!$hasOffers) $hasOffers=true;
            	$quantity += $arOffers["CATALOG_QUANTITY"];
            }

            if($hasOffers) {
				$DB->Query('UPDATE `b_catalog_product` SET `QUANTITY`=' . (int)$quantity . ' WHERE `ID`= ' . (int)$id);
			}
        }    
	}

	public static function updateAll($hash=null)
	{
		if($hash && ($_REQUEST['h'] == $hash)) {
			if (\CModule::IncludeModule('catalog') && \CModule::IncludeModule('iblock') && static::$catalogIBlockId) {
				$rs=\CIBlockElement::GetList(array("SORT"=>"ASC"), ['IBLOCK_ID'=>static::$catalogIBlockId], false, false, array("ID", "CATALOG_QUANTITY"));
				while($product=$rs->Fetch()) {
					$el=new \CIBlockElement;
					$el->Update($product['ID'], ['QUANTITY'=>$product['CATALOG_QUANTITY']]);			
				}
				echo 'Quantity all updated!';
				exit;
			}
		}
	}
}