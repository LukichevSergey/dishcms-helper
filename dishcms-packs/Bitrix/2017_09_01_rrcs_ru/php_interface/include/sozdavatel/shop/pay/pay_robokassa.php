<?
	// class name = CSZDShopPay_<delivery_id>
	// class must have method "GetForm()"
	class CSZDShopPay_robokassa
	{
		static function GetForm($orderID, $orderPrice = false)
		{
			if (!$orderID)
				return false;
				
			if (!$orderPrice)
				$orderPrice = $_SESSION["SZD_SHOP_PAY_INFO_".$orderID]["TOTAL_PRICE"];
			
			$login	= COption::GetOptionString("sozdavatel.shop", "PAYMENT_ROBOKASSA_LOGIN");
			$pass1	= COption::GetOptionString("sozdavatel.shop", "PAYMENT_ROBOKASSA_PASSWORD1");
			$test	= (COption::GetOptionString("sozdavatel.shop", "PAYMENT_ROBOKASSA_TEST") == "Y");
			$crc	= md5($login.":".$orderPrice.":".$orderID.":".$pass1);
			
			$arForm = Array(
				"ACTION" => ($test) ? "http://test.robokassa.ru/Index.aspx": "https://merchant.roboxchange.com/Index.aspx",
				"METHOD" => "post",
				"TARGET" => "_blank",
				"INPUTS_HIDDEN" => Array(
					"FinalStep"			=> "1",
					"MrchLogin"			=> $login,
					"OutSum"			=> $orderPrice,
					"InvId"				=> $orderID,
					"SignatureValue"	=> $crc,
				),
			);
			return $arForm;
		}
		
		static function GetID()
		{
			return "robokassa";
		}
		
		static function GetInfo($orderID, $orderPrice = false)
		{
			$arInfo = Array(
				"ID"			=> CSZDShopPay_robokassa::GetID(),
				"FORM"			=> CSZDShopPay_robokassa::GetForm($orderID, $orderPrice),
				"NAME"			=> iconv("windows-1251", LANG_CHARSET, "Робокасса"),
				"NAME2"			=> iconv("windows-1251", LANG_CHARSET, "Робокассу"),
				"DESCRIPTION"	=> iconv("windows-1251", LANG_CHARSET, "Возврат средств по платежной системе Robokassa невозможен, пожалуйста, будьте внимательны при оплате заказа."),
			);
			return $arInfo;
		}
	}
?>
