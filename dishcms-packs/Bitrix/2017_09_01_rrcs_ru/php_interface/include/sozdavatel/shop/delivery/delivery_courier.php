<?
	// class name = CSZDShopDelivery_<delivery_id>.
	// class must have method "Calculate()", "GetID()", "GetName()".
	class CSZDShopDelivery_courier
	{
		static function Calculate($arBasket)
		{
			// если сумма чека больше установленной суммы, доставка бесплатная
			$deliveryPrice	= intval(COption::GetOptionString(CSZDShop::moduleID, "DELIVERY_PRICE_COURIER"));
			$deliveryLimit	= intval(COption::GetOptionString(CSZDShop::moduleID, "DELIVERY_LIMIT_COURIER"));
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
			return "Доставка курьером";
		}
		
		static function GetID()
		{
			return "courier";
		}
	}
?>
