используется $USER->GetUserGroupArray(), поэтому, если используются разные цены для разных групп пользователей, 
то при генерации мета-тэгов из под администратора, будут получены цены для администратора. Укажите свою группу цены)

include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/iblock/lib/template/functions/fabric.php");
CModule::IncludeModule("iblock");
CModule::IncludeModule("catalog");

$eventManager = \Bitrix\Main\EventManager::getInstance();
$eventManager->addEventHandler(
	"iblock",
	"OnTemplateGetFunctionClass",
	array("SeoFunctionSkuColor", "eventHandler")
);
$eventManager->addEventHandler(
	"iblock",
	"OnTemplateGetFunctionClass",
	array("SeoFunctionPrice", "eventHandler")
);
class SeoFunctionSkuColor extends \Bitrix\Iblock\Template\Functions\FunctionBase 
{
	public static function eventHandler($event)
	{
		$parameters = $event->getParameters();
		$functionName = $parameters[0];
		if ($functionName === "sku_color")
		{
			return new \Bitrix\Main\EventResult(
			\Bitrix\Main\EventResult::SUCCESS,
			"\\SeoFunctionSkuColor"
			);
		}
	}
	public function onPrepareParameters(\Bitrix\Iblock\Template\Entity\Base $entity, $parameters)
	{
		$arguments = array($entity);
		foreach ($parameters as $parameter) {
	 		$arguments[] = $parameter->process($entity);
		}
		return $arguments;
	}
	public function calculate($parameters)
	{
		$color = '';
		
		$result = $this->parametersToArray($parameters);
		$event = array_pop($result);
		if ( $ID = $event->getField('ID') ) {
			$IBLOCK_ID = $event->getField('IBLOCK_ID');
			$arOffers = \CCatalogSKU::getOffersList($ID, $IBLOCK_ID, array(), array('NAME', 'PROPERTY_COLOR'), array("CODE"=>"COLOR"));
			if (!empty($arOffers[$ID])) {
				foreach($arOffers[$ID] as $offerID=>$arOffer) {
					if (isset($arOffer['PROPERTY_COLOR_VALUE'])) {
						$color=$arOffer['PROPERTY_COLOR_VALUE'];
						break;
					}
				}
			}
			if( !$color ) {
				// $dbProps = \CIBlockElement::GetProperty($IBLOCK_ID, $ID, array("SORT"=>"ASC"), array("CODE"=>"SKU_COLOR"));
				// if ($props = $dbProps->GetNext()) {}
			}
		}
		
		return $color ? (' (цвет ' . mb_strtolower($color) . ')') : '';
	}
}
class SeoFunctionPrice extends \Bitrix\Iblock\Template\Functions\FunctionBase 
{
	public static function eventHandler($event)
	{
		$parameters = $event->getParameters();
		$functionName = $parameters[0];
		if ($functionName === "price")
		{
			return new \Bitrix\Main\EventResult(
			\Bitrix\Main\EventResult::SUCCESS,
			"\\SeoFunctionPrice"
			);
		}
	}
	public function onPrepareParameters(\Bitrix\Iblock\Template\Entity\Base $entity, $parameters)
	{
		$arguments = array($entity);
		foreach ($parameters as $parameter) {
	 		$arguments[] = $parameter->process($entity);
		}
		return $arguments;
	}
	public function calculate($parameters)
	{
		global $USER;
		$price = '';
		$result = $this->parametersToArray($parameters);
		$event = array_pop($result);
		if ( $ID = $event->getField('ID') ) {
			$IBLOCK_ID = $event->getField('IBLOCK_ID');
			$arOffers = \CCatalogSKU::getOffersList($ID, $IBLOCK_ID, array(), array('NAME', 'PROPERTY_COLOR'), array("CODE"=>"COLOR"));
			if (!empty($arOffers[$ID])) {
				$minPrice = 0;
				foreach($arOffers[$ID] as $offerID=>$arOffer) {
					$prices = \CCatalogProduct::GetOptimalPrice($offerID, 1, $USER->GetUserGroupArray(), 'N');
					if(isset($prices['RESULT_PRICE']['DISCOUNT_PRICE'])) {
						if ( !$price || ($prices['RESULT_PRICE']['DISCOUNT_PRICE'] < $price)) {
							$price = $prices['RESULT_PRICE']['DISCOUNT_PRICE'];
						}
					}
				}
			}
			if( !$price ) {
				$prices = \CCatalogProduct::GetOptimalPrice($ID, 1, $USER->GetUserGroupArray(), 'N');
				if(isset($prices['RESULT_PRICE']['DISCOUNT_PRICE'])) {
					$price = $prices['RESULT_PRICE']['DISCOUNT_PRICE'];
				}
			}
		}
		return $price ? CurrencyFormat($price, 'RUB') : '';
	}
}
