<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Sale\Location;
use Bitrix\Main\Loader;
use Bitrix\Sale\DiscountCouponsManager;

if (!Loader::includeModule("sale"))
{
	ShowError(GetMessage("SOA_MODULE_NOT_INSTALL"));
	return;
}

$bUseCatalog = Loader::includeModule("catalog");

class KonturSaleOrderConfirmComponent extends CBitrixComponent
{
	public function GetItems($arParams)
	{
		$arItems=array();

		return $arItems;
	}
}