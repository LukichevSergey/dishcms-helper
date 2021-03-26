<?
	// class name = CSZDShopDelivery_<delivery_id>.
	// class must have method "Calculate()", "GetID()", "GetName()".
	class CSZDShopDelivery_post
	{
		static function Calculate($arBasket)
		{
			// если сумма чека больше установленной суммы, доставка бесплатная
			$deliveryPrice	= intval(COption::GetOptionString(CSZDShop::moduleID, "DELIVERY_PRICE_POST"));
			$deliveryLimit	= intval(COption::GetOptionString(CSZDShop::moduleID, "DELIVERY_LIMIT_POST"));
			if ((intval($arBasket["TOTAL_PRICE"]) - intval($arBasket["DELIVERY_PRICE"])) >= $deliveryLimit)
			{
				return 0;
			}
			else
			{
				return $deliveryPrice;
			}
		}
		
		static function GetName()
		{
			return "Доставка почтой";
		}
		
		static function GetID()
		{
			return "post";
		}
	}
?>
