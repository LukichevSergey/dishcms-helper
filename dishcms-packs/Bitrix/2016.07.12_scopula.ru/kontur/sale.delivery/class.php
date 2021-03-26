<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

class SaleDeliveryComponent extends CBitrixComponent
{
	public function GetItems($arParams)
	{
		global $APPLICATION;

		$arItems=array();

		if(\Bitrix\Main\Loader::includeModule('sale')) {
			$rsDelivery=CSaleDelivery::GetList(
				array("SORT"=>"ASC"),
				array("ACTIVE"=>"Y")
			);
			while($arDelivery=$rsDelivery->GetNext()) {
				$arItems[]=$arDelivery;
			}
		}

		return $arItems;
	}
}