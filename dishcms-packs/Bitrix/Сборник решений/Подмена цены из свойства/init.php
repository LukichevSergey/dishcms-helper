<?php
AddEventHandler("catalog", "OnGetDiscount", array("initCatalogCalcPrice", "onGetDiscount"));
AddEventHandler("catalog", "OnCountPriceWithDiscount", array("initCatalogCalcPrice", "OnCountPriceWithDiscount"));
AddEventHandler("sale", "OnBeforeBasketUpdateAfterCheck", array("initCatalogCalcPrice", "OnBeforeBasketUpdateAfterCheck"));
class initCatalogCalcPrice {
	static private $iblockId = 2;
	static private $price = null;
	static private $inupdate = false;	
	
	public static function onGetDiscount($intProductID, $intIBlockID, $arCatalogGroups, $arUserGroups, $strRenewal, $siteID, $arDiscountCoupons, $boolSKU, $boolGetIDS) 
	{
		$saleprice=CIBlockElement::GetProperty($intIBlockID, $intProductID, array(), array('CODE'=>'SALEPRICE'))->Fetch();
   		self::$price = empty($saleprice['VALUE']) ? null :  (float)$saleprice['VALUE'];
   		return true;
    }
    
    public static function OnCountPriceWithDiscount($price, $currency, $arDiscounts)
    {
    	if (self::$price) {
    		return self::$price;
    	}
    	return true;
    }

	public static function OnBeforeBasketUpdateAfterCheck($id, $arFields)
    {
    	if( !self::$inupdate ) {
			$item = CSaleBasket::GetByID($id);
			$saleprice=CIBlockElement::GetProperty(self::$iblockId, $item['PRODUCT_ID'], array(), array('CODE'=>'SALEPRICE'))->Fetch();
			if ( !empty($saleprice['VALUE']) ) {	
				self::$inupdate = true;		
				CSaleBasket::Update($id, array('PRICE'=>$saleprice['VALUE']));
			}
		}
		self::$inupdate = false;
    }
}

