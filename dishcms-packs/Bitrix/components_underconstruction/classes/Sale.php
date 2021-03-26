<?
namespace Kontur;

if (!\CModule::IncludeModule("sale"))
{
	ShowError(GetMessage("SALE_MODULE_NOT_INSTALL"));
	return;
}

class Sale 
{
	public static function GetBasketItems($arSelectFields=array(
		"ID", "CALLBACK_FUNC", "MODULE", "PRODUCT_ID", "QUANTITY", "DELAY", "CAN_BUY", "PRICE"))
	{
		$arBasketItems = array();

		$dbBasketItems = \CSaleBasket::GetList(
        	array(
                "NAME" => "ASC",
                "ID" => "ASC"
            ),
	        array(
                "FUSER_ID" => \CSaleBasket::GetBasketUserID(),
                "LID" => SITE_ID,
                "ORDER_ID" => "NULL"
            ),
    	    false,
        	false,
	        $arSelectFields
	    );
		while ($arItems = $dbBasketItems->Fetch())
		{
		   $arBasketItems[] = $arItems;
		}

		return $arBasketItems;
	}

	public static function GetBasketItem($ID, $arSelectFields=array(
        "ID", "CALLBACK_FUNC", "MODULE", "PRODUCT_ID", "QUANTITY", "DELAY", "CAN_BUY", "PRICE"))
    {
        $dbBasketItems = \CSaleBasket::GetList(
            array(
                "NAME" => "ASC",
                "ID" => "ASC"
            ),
            array(
				"ID"=>$ID,
                "FUSER_ID" => \CSaleBasket::GetBasketUserID(),
                "LID" => SITE_ID,
                "ORDER_ID" => "NULL"
            ),
            false,
            false,
            $arSelectFields
        );
        return $dbBasketItems->Fetch();
    }

	public static function getBasketTotalQuantity()
	{
		$totalQuantity=0;

		$arItems=self::GetBasketItems(array("ID", "QUANTITY"));
		if(!empty($arItems)) {
			foreach($arItems as $arItem) 
				$totalQuantity += (int)$arItem["QUANTITY"];
		}

		return $totalQuantity;
	}
}
