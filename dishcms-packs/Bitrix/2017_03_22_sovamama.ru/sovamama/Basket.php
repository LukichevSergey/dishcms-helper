<?php
/**
 * Корзина
 */
namespace sovamama;

use Kontur\Helper as H;
use Kontur\Core\Iblock\Tools\Db;
use Kontur\Core\Main\Data\Cache;

class Basket
{
	/**
	 * @var int идентификатор группы оптового покупателя.
	 */
	public static $OPT_GROUPS = array(9);
	
	/**
	 * @access protected
	 * @var array кэш
	 */
    protected static $_cache = array();
		// array комплекты вида array(идентификатор_товара => array(идентификатор_предложения, идентификатор_предложения2))
    	// 'COMPLECTS' => false,
		// array массив идентификаторов товаров предложений вида array(идентификатор_предложения => идентификатор_товара)
    	//'OFFER_PRODUCT_IDS' => false,
		// array комплекты по предложениям вида 
		// array(идентификатор_предложения => array(идентификатор_предложения_в_комплекте, идентификатор_предложения_в_комплекте_2))
    	//'COMPLECTS_BY_OFFER' => false,
    	// array данные о кол-ве товаров в корзине вида array(идентификатор_предложения => кол-во_данного_предложения_в_корзине).
    	//'QUANTITIES' => false,
    	// array записи корзины
    	//'BASKET_ITEMS' => false,
    	// array идентификаторы предложений всех записей корзины.
    	//'BASKET_PRODUCT_IDS' => false,
    	// array массив заголовков комплектов вида array(идентификатор_товара=>заголовок_товара)
    	//'COMPLECT_TITLES' => false,
    //);
    
    public static function getDefaultCatalogGroupId()
    {
    	return 5;
    }
    
    /**
     * Получить идентификатор типа цены 
     * @param integer $productId идентификатор товара при $isSizeComplect=true, либо 
     * идентификатор предложения, при $isSizeComplect=false.
     * @param boolean $isSizeComplect является комплектом размерного ряда.
     * @param boolean $refresh обновить данные. По умолчанию (FALSE) не обновлять.
     * @return integer идентификатор цены.
     */
    public static function getCatalogGroupId($productId, $isSizeComplect=false, $catalogGroupId=false, $refresh=false)
    {
    	// Отпускные розничные
    	if(!$catalogGroupId) {
    		$catalogGroupId = self::getDefaultCatalogGroupId();
    	}
    	$defaultCatalogGroupId = $catalogGroupId;
    	
    	if (\CSite::InGroup(self::$OPT_GROUPS)) {
    		if ($isSizeComplect) {
	    		$totalPrice = self::getComplectTotalPrice($productId, $catalogGroupId, $refresh);
	    		if (($totalPrice >= 3000) && ($totalPrice < 10000)) {
	    			// Опт 3трр
	    			$catalogGroupId = 6;
	    		}
	    		elseif ($totalPrice >= 10000) {
	    			// Опт 10трр
	    			$catalogGroupId = 7;
	    		}
	    		else {
	    			$catalogGroupId = self::getDefaultCatalogGroupId();
	    		}
	    	}
	    	else {
	    		$totalPrice = self::getSingleTotalPrice($productId, $catalogGroupId, $refresh);
	    		if ($totalPrice >= 3000) {
	    			// Опт 3брр
	    			$catalogGroupId = 8;
	    		}
	    		else {
	    			$catalogGroupId = self::getDefaultCatalogGroupId();
	    		}
	    	}
	    	
	    	// если идентификатор типа цены изменился, делаем перерасчет общей стоимости,
	    	// для того, чтобы определить новая пересчитанная общая стоимость действительно 
	    	// находится в пределах действия скидки или нет.
	    	if(($catalogGroupId != self::getDefaultCatalogGroupId()) 
	    		&& ($catalogGroupId != $defaultCatalogGroupId)) 
	    	{
	    		$catalogGroupId = self::getCatalogGroupId($productId, $isSizeComplect, $catalogGroupId, false);
	    	}
    	}
    	
    	return $catalogGroupId;
    }
    
    /**
     * Обновление кэша данного класса
     */
    public static function refresh()
    {
    	self::$_cache = array(
			'COMPLECTS' => false,
			'COMPLECTS_BY_OFFER' => false,
    		'QUANTITIES' => false,
	    	'BASKET_ITEMS' => false,
	    	'BASKET_PRODUCT_IDS' => false,
	    	'OFFER_PRODUCT_IDS' => false,
	    	'COMPLECT_TITLES' => false,
    	);
    }
    
    /**
     * Получить коэффициент единицы измерения товара.
     * @param integer $id идентификатор товара.
     * @return float коэффициент единицы измерения товара.
     */
    public static function getMeasureRatioByProductId($id)
    {
    	$measureRatio = 1;
    	
    	if($rsMeasureRatio = CCatalogMeasureRatio::getList(array(), array("PRODUCT_ID" => $id), false, false, array())) {
    		$measure = $rsMeasureRatio->Fetch();
    		if(!empty($measure["RATIO"])) {
    			$measureRatio = (float)$measure["RATIO"];
    		}
    	}
    	
    	return $measureRatio;
    }
    
    /**
     * Получить идентификаторы предложений всех записей корзины.
     * @return array идентификаторы предложений всех записей корзины.
     */
    public static function getBasketItemsProductIds()
    {
//     	if (self::$_cache['BASKET_PRODUCT_IDS'] !== false) {
//     		return self::$_cache['BASKET_PRODUCT_IDS'];
//     	}
    	
    	$ids = array();
    	
    	$items = self::getBasketItems();
    	if (!empty($items)) {
    		foreach ($items as $item) {
    			if(!empty($item['PRODUCT_ID'])) {
    				$ids[] = (int)$item['PRODUCT_ID'];
    			}
    		}
    	}
    	
    	self::$_cache['BASKET_PRODUCT_IDS'] = $ids;
    	
    	return $ids;
    }
    
    /**
     * Получить идентификаторы предложений размерного ряда.
     * @param $offerId идентификатор предолжения
     * @return array массив идентификаторов предложений входящих в размерный ряд.
     */
    public static function getComplectOfferIds($offerId)
    {
//     	if (isset(self::$_cache['COMPLECTS_BY_OFFER'][$offerId])) {
//     		return self::$_cache['COMPLECTS_BY_OFFER'][$offerId];
//     	}
    	
        $offerIds = array();
        
        $productId = false;
    	if ($productInfo=\CCatalogSKU::GetProductInfo($offerId)) {
    		$productId = $productInfo['ID'];
    		if($offers = \CCatalogSKU::getOffersList($productId, $productInfo['IBLOCK_ID'])) {
    			foreach ($offers[$productId] as $offer) {
    				$offerIds[] = (int)$offer['ID'];
    			}
    		}
    	}
        
        if (!empty($offerIds)) {
        	foreach ($offerIds as $id) {
        		self::$_cache['COMPLECTS_BY_OFFER'][$id] = $offerIds;
        		self::$_cache['OFFER_PRODUCT_IDS'][$id] = $productId;
        	}
        }
        else {
        	self::$_cache['COMPLECTS_BY_OFFER'][$offerId] = array();
        	self::$_cache['OFFER_PRODUCT_IDS'][$offerId] = $productId;
        }

        return $offerIds;
    }
    
    /**
     * Получить заголовок комплекта
     * @param integer $offerId идентификатор предложения
     */
    public static function getComplectTitle($offerId)
    {
    	$productId = self::getProductId($offerId);
    	
//     	if(self::$_cache['COMPLECT_TITLES'][$productId] !== false) {
//     		return self::$_cache['COMPLECT_TITLES'][$productId];
//     	}
    	
    	$product=Db::fetchAll(\CIBlockElement::GetByID($productId));
    	
    	self::$_cache['COMPLECT_TITLES'][$productId] = $product['NAME'];
    	
    	return $product['NAME'];
    }
    
    /**
     * Получить идентификатор товара
     * @param integer $offerId идентификатор предложения
     */
    public static function getProductId($offerId)
    {
//     	if (self::$_cache['OFFER_PRODUCT_IDS'][$offerId] !== false) {
//     		return self::$_cache['OFFER_PRODUCT_IDS'][$offerId];
//     	}
    	
    	$productId = false;
    	
    	if($productInfo = \CCatalogSku::GetProductInfo($offerId)) {
    		$productId = $productInfo['ID'];
    	}
    	
    	self::$_cache['OFFER_PRODUCT_IDS'][$offerId] = $productId;
    	
    	return $productId;
    }
    
    /**
     * Получить все записи корзины
     * @param array $arSelectFields \CSaleBasket::GetList()::$arSelectFields
     * @param array $properties массив дополнительных свойств записи корзины, вида 
     * array(код_свойства, код_свойства_2)
     * @return array массив записей корзины (с дополнительным параметром "PROPERTIES")
     * вида array(идентификатор_записи_в_корзине=>массив_параметров)
     */
    public static function getBasketItems($arSelectFields=array(), $properties=array())
    {
//     	$cacheHash=self::_getCacheHash([$arSelectFields, $properties]);
//     	if (isset(self::$_cache['BASKET_ITEMS'][$cacheHash])) {
//     		return self::$_cache['BASKET_ITEMS'][$cacheHash];
//     	}
    	
        $items = array();
        
        $rsItems=\CSaleBasket::GetList(
        	array(), 
        	array(
        		'FUSER_ID'=>\CSaleBasket::GetBasketUserID(),
        		'LID' => SITE_ID,
        		'ORDER_ID' => 'NULL'
        	), 
        	false, 
        	false, 
        	$arSelectFields
        );
        
        if ($rsItems) {
        	$items=Db::fetchAll($rsItems, 'ID');
        }
        
       	foreach ($items as $basketId=>$item) {
       		if ($rsPropsList = \CSaleBasket::GetPropsList(array("SORT" => "ASC", "NAME" => "ASC"), array("BASKET_ID" => $item["ID"]), false, false, $properties)) {
       			foreach(Db::fetchAll($rsPropsList) as $property) {
       				if(empty($properties) || in_array($property["CODE"], $properties)) {
       					$items[$basketId]['PROPERTIES'][$property["CODE"]] = $property;
       				}
       			}
       		}
       	}
        
        self::$_cache['BASKET_ITEMS'][$cacheHash]=$items;

        return $items;
    }
    
    /**
     * Получить общую стоимость размерного ряда.
     * @param integer $offerId идентификатор предложения размерного ряда.
     * @param integer $catalogGroupId идентификатор тип цены. По умолчанию (FALSE) будет 
     * получен self::getDefaultCatalogGroupId().
     * @param boolean $refresh обновить данные. По умолчанию (FALSE) не обновлять.
     * @return float
     */
    public static function getComplectTotalPrice($offerId, $catalogGroupId=false, $refresh=false)
    {
    	$totalPrice = 0;
    	
    	if(!$catalogGroupId) {
    		$catalogGroupId = self::getDefaultCatalogGroupId();
    	}
    	
    	$basketItems = self::getBasketItems();
    	$basketData = self::getBasketData($refresh);
    	
    	$hash = self::_getItemHash($offerId, true);
    	if (isset($basketData['COMPLECTS'][self::getProductId($offerId)])) {
    		foreach ($basketData['COMPLECTS'][self::getProductId($offerId)] as $item) {
   				$price = self::getProductPrices($item['PRODUCT_ID'], $catalogGroupId);
   				$totalPrice += $price['PRICE'] * $item['QUANTITY'];
    		}
    	}
    	
    	return $totalPrice;
    }
    
    /**
     * Получить общую стоимость одиночного товара.
     * @param integer $offerId идентификатор предложения.
     * @param integer $catalogGroupId идентификатор тип цены. По умолчанию (FALSE) будет 
     * получен self::getDefaultCatalogGroupId().
     * @param boolean $refresh обновить данные. По умолчанию (FALSE) не обновлять.
     * @return float
     */
    public static function getSingleTotalPrice($offerId, $catalogGroupId=false, $refresh=false)
    {
    	$totalPrice = 0;

    	if(!$catalogGroupId) {
    		$catalogGroupId = self::getDefaultCatalogGroupId();
    	}
    	
    	$basketData = self::getBasketData($refresh);
    	 
    	if (isset($basketData['SINGLES'][$offerId])) {
   			$price = self::getProductPrices($offerId, $catalogGroupId); 
   			$totalPrice = $price['PRICE'] * $basketData['SINGLES'][$offerId]['QUANTITY'];
    	}
    	 
    	return $totalPrice;
    }
    
    /**
     * Получить все цены товара или цену товара, если передан $catalogGroupId. 
     * @param integer $productId идентификатор товара.
     * @param integer $catalogGroupId идентификатор типа цены. 
     * По умолчанию (FALSE) будут возвращены все типы цен. 
     * @return array 
     */
    public static function getProductPrices($productId, $catalogGroupId=false)
    {
    	if($catalogGroupId && isset(self::$_cache['PRODUCT_PRICES'][$productId][$catalogGroupId])) {
			return self::$_cache['PRODUCT_PRICES'][$productId][$catalogGroupId];	
    	}
    	
    	if(!$catalogGroupId && isset(self::$_cache['PRODUCT_PRICES'][$productId])) {
    		return self::$_cache['PRODUCT_PRICES'][$productId];
    	}
    	
    	$prices = array();
    	
    	// получаем все типы цен, возможные для данного товара
    	if($rs = \CPrice::GetListEx(array(), array('PRODUCT_ID'=>$productId))) {
    		$prices = Db::fetchAll($rs, 'CATALOG_GROUP_ID');
    	}
    	
    	self::$_cache['PRODUCT_PRICES'][$productId] = $prices;
    	
    	if($catalogGroupId) {
    		if(isset(self::$_cache['PRODUCT_PRICES'][$productId][$catalogGroupId])) {
    			return self::$_cache['PRODUCT_PRICES'][$productId][$catalogGroupId];
    		}
    		else {
    			return array();
    		}	
    	}
    	
    	return $prices;
    }

    /**
     * Получить данные о кол-ве товаров в корзине
     * @return array массив вида array(идентификатор_предложения => кол-во_данного_предложения_в_корзине)
     */
    public static function getBasketQuantities()
    {
//     	if (self::$_cache['QUANTITIES'] !== false) {
//     		return self::$_cache['QUANTITIES'];
//     	}
    	
        $quantities = array();
        
        $items = self::getBasketItems();
        if (!empty($items)) {
        	foreach ($items as $item) {
        		if (isset($quantities[$item['PRODUCT_ID']])) {
        			$quantities[$item['PRODUCT_ID']] += (int)$item['QUANTITY']; 
        		}
        		elseif(!empty($item['PRODUCT_ID'])) {
        			$quantities[$item['PRODUCT_ID']] = (int)$item['QUANTITY'];
        		}
        	}
        }
        
        self::$_cache['QUANTITIES'] = $quantities;

        return $quantities;
    }
    
    /**
     * Добавление в корзину
     * @param integer|array $ids идентификатор товара, или массив идентификаторов товаров
     * добавляемых в корзину
     * @param integer|array $quantities кол-во добавляемого товара, или массив с кол-вом
     * добавляемого товара вида array(идентификатор_товара=>кол-во). По умолчанию 1(один).
     * @return boolean товары успешно добавлены в корзину (TRUE) или произошла ошибка 
     * при добавлении (FALSE).
     */
    public static function add($ids, $quantities=1, $isSizeComplect=false)
    {
    	$result = array(
    		'hasErrors' => false,
    		'changed' => false
    	);
    	
    	if(!is_array($ids)) {
    		$ids = array($ids);
    	}
    	
    	foreach($ids as $idx=>$id) {
    		if(is_array($quantities)) {
    			$quantity = isset($quantities[$idx]) ? $quantities[$idx] : 1;
    		}
    		else {
    			$quantity = ((int)$quantities > 0) ? $quantities : 1; 
    		}
    		
    		$basketId = Add2BasketByProductID(
    			intval($id), 
    			$quantity, 
    			array(), 
    			self::_generateBasketItemProperties($id, $isSizeComplect)
    		);
    			
    		if(!$basketId) {
    			$result['hasErrors'] = true;
    		}
    	}
    	
    	if(!$result['hasErrors']) {
    		$result['changed'] |= self::recalc($basketId);
    	}
    	
    	return $result;
    }
    
    /**
     * Очистка корзины текущего пользователя
     * @return boolean
     */
    public static function clearBasket()
    {
    	return \CSaleBasket::DeleteAll(\CSaleBasket::GetBasketUserID());
    }

    /**
     * Группировка по комплектам и одиночным товарам, а также пересчет 
     * цен записей в корзине.
     * @param integer|FALSE $basketId Идентификатор записи в корзине, над 
     * которой производились действия, либо FALSE.
     * @return boolean возвращает изменилась ли структура корзины или нет.
     * TRUE - изменилась, FALSE - не изменилась.
     * 
     * Может быть также использован для событий:
     * AddEventHandler("sale", "OnBasketAdd", array("\sovamama\Basket", "recalc"));
	 * AddEventHandler("sale", "OnBasketUpdate", array("\sovamama\Basket", "recalc"));
	 * AddEventHandler("sale", "OnBasketDelete", array("\sovamama\Basket", "recalc"));
     */
    public static function recalc($basketId=false)
    {
    	// если пользователь не является оптовым покупателем не выполняем никакого перерасчета
    	if (!\CSite::InGroup(self::$OPT_GROUPS)) {
    		return false;
    	}
    	
    	return self::checkAndUpdateBasket();
    }
    
    /**
     * Получить массив для обновления цены товара.
     * @param unknown $productId
     * @param string $isSizeComplect
     * @return multitype:multitype:string unknown  multitype:string unknown Ambigous <number, string> Ambigous <multitype:, multitype:unknown >
     */
    public static function getProductPrice($productId, $isSizeComplect=false)
    {
    	$catalogGroupId = self::getCatalogGroupId($productId, $isSizeComplect);
    	$price = self::getProductPrices($productId, $catalogGroupId);
    	
    	return array(
    		'PRICE' => array(
    			'ID' => $productId,
    			'CATALOG_GROUP_ID' => $catalogGroupId,
    			'PRICE' => $price,
    			'CURRENCY' => 'RUB',
    			'ELEMENT_IBLOCK_ID' => $productId,
    			'VAT_INCLUDED' => 'Y',
    		),
    		'DISCOUNT_PRICE' => array(
    			'VALUE' => $price,
    			'CURRENCY' => 'RUB',
    		),
    	);
    }
    
    /**
     * Проверяет изменилась ли структура корзины, и если изменилась, 
     * то обновляет записи корзины.
     * @return boolean возвращает TRUE если структура корзины изменилась, 
     * или FALSE если структура корзины не изменилась.
     */
    public static function checkAndUpdateBasket()
    {
    	$changed = false;
    	
    	$basketData = self::getBasketDataByBasket();
    	$newBasketData = self::getBasketData();
    	
    	foreach($newBasketData['COMPLECTS'] as $productId=>$items) {
    		foreach($items as $hash=>$item) {
    			if(!isset($basketData['COMPLECTS'][$productId][$hash])) {
    				$changed = true;
    			} 
    			else {
    				if(isset($basketData['COMPLECTS'][$productId][$hash]['PRICES']['PRICE']['PRICE'])) {
    					$price=$basketData['COMPLECTS'][$productId][$hash]['PRICES']['PRICE']['PRICE'];
    					if($price != $item['PRICES']['PRICE']['PRICE']) {
    						$changed = true;
    					}
    				}
    				else {
    					$changed = true;
    				}
    			}
    			
    			if($changed) {
    				break;
    			}
    		}
    		
   			if($changed) {
    			break;
    		}
    	}
    	
    	if(!$changed) {
    		// @var array $items массив текущих одиночных товаров корзины вида array(идентификатор_предложения=>цена)
    		$items = array();
    		
    		// @var array $newItems массив новыйх одиночных товаров корзины вида array(идентификатор_предложения=>цена)
    		$newItems = array();
    		
    		foreach($basketData['SINGLES'] as $offerId=>$item) {
    			if(!isset($item['PRICES']['PRICE']['PRICE'])) {
    				$changed = true;
    				break;
    			}
    			$items[$offerId] = $item['PRICES']['PRICE']['PRICE'];
    		}
    		
    		if(!$changed) {
    			foreach($newBasketData['SINGLES'] as $offerId=>$item) {
    				if(!isset($item['PRICES']['PRICE']['PRICE'])) {
    					$changed = true;
    					break;
    				}
    				$newItems[$offerId] = $item['PRICES']['PRICE']['PRICE'];
    			}
    		}
    	}
    	
    	if($changed) {
    		// обновляем корзину
    	}
    	
    	return $changed;
    }
    
    /**
     * Получить данные корзины сгруппированные по комплектам и одиночным 
     * товарам по текущим записям корзины.
     * @return array массив вида array(
     * 	"COMPLECTS"=>array(
     * 		PRODUCT_ID=>array(
     * 			BASKET_ITEM_ID=>array(BASKET_ITEM)
     * 			BASKET_ITEM_ID=>array(BASKET_ITEM)
     * 			BASKET_ITEM_ID=>array(BASKET_ITEM)
     * 			BASKET_ITEM_ID=>array(BASKET_ITEM)
     * 		)
     * 	),
     *  "SINGLES"=>array(
     *  	OFFER_ID=>array(BASKET_ITEM)
     *  )
     * )
     */
    public static function getBasketDataByBasket()
    {
    	// @var array $items массив записей корзины в формате возвращаемого результата.
    	$items = array();

    	$basketItems = self::getBasketItems();
    	foreach ($basketItems as $basketId=>$item) {
    		if($isSizeComplect = H::aget($item, 'PROPERTIES.IS_SIZE_COMPLECT.VALUE', false)) {
    			$hash = H::aget($item, 'PROPERTIES.HASH.VALUE');
    			$productId = H::aget($item, 'PROPERTIES.IS_PRODUCT_ID.VALUE');
    			$items['COMPLECTS'][$productId][$hash] = $item;
    		}
    		else {
    			$items['SINGLES'][$item['PRODUCT_ID']] = $item;
    		}
    	}
    	
    	return $items;
    }
    
    /**
     * Получить данные корзины сгруппированные по комплектам и одиночным товарам.
     * @param boolean $refresh получить данные с обновлением кэша.
     * @return array массив вида array(
     * 	"COMPLECTS"=>array(
     * 		PRODUCT_ID=>array(
     * 			BASKET_ITEM_ID=>array(BASKET_ITEM)
     * 			BASKET_ITEM_ID=>array(BASKET_ITEM)
     * 			BASKET_ITEM_ID=>array(BASKET_ITEM)
     * 			BASKET_ITEM_ID=>array(BASKET_ITEM)
     * 		)
     * 	),
     *  "SINGLES"=>array(
     *  	OFFER_ID=>array(BASKET_ITEM)
     *  )
     * )
     */
    public static function getBasketData($refresh=true)
    {
    	// @var array $complectItems массив комплектов корзины
    	$complectItems = array();
    	
    	// @var array $singleItems массив одиночных товаров корзины
    	$singleItems = array();
    	
    	// @var array $quantities массив кол-ва всех записей в корзине 
    	// вида array(идентификатор_предложения => кол-во_данного_предложения_в_корзине)
    	$quantities = self::getBasketQuantities();
    	
    	// @var array $processedOfferIds массив идентификаторов обработанных предложений. 
    	$processedOfferIds = array();
    	$basketItems = self::getBasketItems(); 
    	foreach ($basketItems as $basketId=>$item) {
    		// т.к. для одного товара из комплекта (за один проход цикла) формируется сразу и данные о комплекте и данные об одиночных товарах,
    		// то для всех последующих товаров из комплекта, переходим к следующей итерации цикла.
    		if (in_array($item["PRODUCT_ID"], $processedOfferIds)) {
    			continue;
    		}
    	
    		// @var array $complectOfferIds список идентификаторов предложений комплекта текущего товара (текущий комплект).
    		$complectOfferIds = self::getComplectOfferIds($item["PRODUCT_ID"]);
    		$processedOfferIds = array_merge($processedOfferIds, $complectOfferIds);
    	
    		// @var array $complect массив комплекта с кол-вом товаров в корзине вида array(идентификатор_предложения => кол-во_в_корзине)
    		$complect = array();
    		foreach ($complectOfferIds as $offerId) {
    			$complect[$offerId] = isset($quantities[$offerId]) ? $quantities[$offerId] : 0;
    		}
    		asort($complect);
    	
    		// @var int $complectQuantity кол-во товара текущего комплекта.
    		$complectQuantity = false;
    		// @var array $singleQuantities массив одиночных товаров с указанием кол-ва вида array(идентификатор_предложения => во_в_корзине)
    		$singleQuantities = array();
    		foreach ($complect as $offerId=>$quantity) {
    			if ($complectQuantity === false) {
    				$complectQuantity = $quantity;
    			}
    			
    			if (!$complectQuantity || ($quantity > $complectQuantity)) {
    				$singleQuantities[$offerId]=$quantity - $complectQuantity;
    			}
    		}
    	
    		// если кол-во комплектов больше нуля, заполняем данные о комплектах.
    		if ($complectQuantity > 0) {
    			foreach ($complectOfferIds as $offerId) {
    				if(!isset($complectItems[self::getProductId($offerId)])) {
    					$complectItems[self::getProductId($offerId)] = array();
    				}
    				
    				$complectItems[self::getProductId($offerId)][self::_getItemHash($offerId, true)] = array(
    					"PRODUCT_ID" => $offerId,
    					"QUANTITY" => $complectQuantity,
    					"PRICES" => self::getProductPrice($offerId, true),
    					"PROPERTIES" => self::_generateBasketItemProperties($offerId, true)
    				);
    			}
    		}
    		
    		// заполняем данные об одиночных товарах
    		if (!empty($singleQuantities)) {
    			foreach ($singleQuantities as $offerId=>$quantity) {
    				if($quantity > 0) {
	    				$singleItems[$offerId] = array(
	    					"PRODUCT_ID" => $offerId,
	    					"QUANTITY" => $quantity,
	    					"PRICES" => self::getProductPrice($offerId, false),
	    					"PROPERTIES" => self::_generateBasketItemProperties($offerId, false)
	    				);
    				}
    			}
    		}
    	}
    	
    	return array(
    		"COMPLECTS"=>$complectItems,
    	 	"SINGLES"=>$singleItems
    	);    	   
    }
    
    /**
     * OnGetOptimalPrice
     * @see 
     */
    public static function getOptionalPrice($productID, $quantity=1, $arUserGroups=array(), $renewal="N", $arPrices=array(), $siteID=false, $arDiscountCoupons=false)
    {
    	return self::getProductPrice($productID, false);
    }
    
    /**
     * Генерация дополниьтельных свойств записи корзины. 
     * @param integer $offerId идентификатор предложения
     * @param boolean $isSizeComplect является комплектом размерного ряда. 
     * По умолчанию (FALSE) не является комплектом размерного ряда.
     * @return array массив дополнительных свойств записи корзины вида:
     * array(
     * 	array(
     * 		"NAME" => "Заголовок",
	 *		"CODE" => "Код свойства",
	 *		"VALUE" => Значение свойства,
	 * 		"SORT" => Порядковый номер сортировки 
     * 	)
     * )
     */
    protected static function _generateBasketItemProperties($offerId, $isSizeComplect=false)
    {
    	return array(
			array(
		        "NAME" => "Хэш товара в корзине",
		        "CODE" => "HASH",
		        "VALUE" => self::_getItemHash($offerId, $isSizeComplect),
		        "SORT" => 100
		    ),
		    array(
		        "NAME" => "Является размерным рядом",
		        "CODE" => "IS_SIZE_COMPLECT",
		        "VALUE" => ($isSizeComplect ? "Y" : "N"),
		        "SORT" => 200
		    ),
		    array(
		        "NAME" => "Идентификатор товара размерного ряда",
		        "CODE" => "SIZE_COMPLECT_PRODUCT_ID",
		        "VALUE" => self::getProductId($offerId),
		        "SORT" => 300
		    ),
		    array(
		        "NAME" => "Заголовок комплекта",
		        "CODE" => "SIZE_COMPLECT_TITLE",
		        "VALUE" => self::getComplectTitle($offerId),
		        "SORT" => 400
		    )
		);
    } 
    
    /**
     * Получить (сгенерировать) хэш записи в корзине
     * @param integer $offerId идентификатор предложения
     * @param boolean $isSizeComplect является комплектом размерного ряда
     * По умолчанию (FALSE) не является комплектом размерного ряда.
     * @return string
     */
    protected static function _getItemHash($offerId, $isSizeComplect=false)
    {
    	return $offerId . '_' . ($isSizeComplect ? 'c' : 's');
    }
    
    /**
     * Получить хэш кэша.
     * @param string $data данные для генерации хэша кэша.
     */
    protected static function _getCacheHash($data=false)
    {
    	return md5(serialize($data));
    } 
}