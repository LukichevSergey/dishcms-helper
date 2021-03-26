<?
/**
 * Basket
 *
 * @link https://mrcappuccino.ru/blog/post/work-with-basket-bitrix-d7
 */
namespace Kontur\Core\Sale;

\Bitrix\Main\Loader::includeModule('sale');

class Order 
{
    public static function addOrderProperties($orderId, $arOrderProps)
    {
    	if(!empty($arOrderProps)) {
    		$props=self::getProperties([], ['ID', 'CODE', 'NAME']);
    		if(!empty($props)) {
    			foreach($props as $code=>$prop) {
    				if(array_key_exists($code, $arOrderProps)) {
    					self::addOrderProperty($orderId, $prop, $arOrderProps[$code]);
    				}
    			} 
    		}
    	}
    }
    
    public static function addOrderProperty($orderId, $property, $value)
    {
    	return \CSaleOrderPropsValue::Add(array(
   			'NAME' => $property['NAME'],
   			'CODE' => $property['CODE'],
  			'ORDER_PROPS_ID' => $property['ID'],
   			'ORDER_ID' => $orderId,
   			'VALUE' => $value,
    	));
    }
    
    public static function getProperties($filter=[], $select=[])
    {
    	$props=[];
    	
    	$rs=\CSaleOrderProps::GetList([], $filter, false, false, $select);
    	while($prop=$rs->Fetch()) {
    		if(!empty($prop['CODE'])) {
    			$props[ $prop['CODE'] ]=$prop;
    		}
    		else {
    			$props[]=$prop;
    		}
    	}
    	
    	return $props;
    }
}