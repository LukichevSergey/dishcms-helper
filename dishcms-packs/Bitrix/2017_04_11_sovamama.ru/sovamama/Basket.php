<?php
/**
 * Корзина
 */
namespace sovamama;

use Kontur\Helper as H;
use Kontur\HCatalog;
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
    
    /**
     * Получить иденификатор типа цены по умолчанию
     * @return integer
     */
    public static function getDefaultCatalogGroupId()
    {
    	return 5;
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
     * Получить идентификатор типа цены.
     * Метод РАССЧЕТА цены. 
     * @param integer $offerId идентификатор предложения.
     * @param boolean $isSizeComplect является комплектом размерного ряда.
     * @param boolean $refresh обновить данные. По умолчанию (FALSE) не обновлять.
     * @return integer идентификатор цены.
     */
    public static function getCatalogGroupId($offerId, $isSizeComplect=false, $catalogGroupId=false, $refresh=false)
    {
    	// Отпускные розничные
    	if(!$catalogGroupId) {
    		$catalogGroupId = self::getDefaultCatalogGroupId();
    	}
    	$defaultCatalogGroupId = $catalogGroupId;
    	
    	if (\CSite::InGroup(self::$OPT_GROUPS)) {
    		if ($isSizeComplect) {
    			$totalPrice = self::getComplectTotalPrice(HCatalog::getProductId($offerId), $catalogGroupId, $refresh);
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
	    		$totalPrice = self::getSingleTotalPrice($offerId, $catalogGroupId, $refresh);
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
     * Получить идентификаторы предложений всех записей корзины.
     * @return array идентификаторы предложений всех записей корзины.
     */
    public static function getBasketItemsProductIds()
    {
    	if(self::_cacheHas('BASKET_PRODUCT_IDS')) {
    		return self::_cacheGet('BASKET_PRODUCT_IDS', array());
    	}
    	
    	$ids = array();
    	
    	$items = self::getBasketItems();
    	if (!empty($items)) {
    		foreach ($items as $item) {
    			if(!empty($item['PRODUCT_ID'])) {
    				$ids[] = (int)$item['PRODUCT_ID'];
    			}
    		}
    	}
    	
    	self::_cacheSet('BASKET_PRODUCT_IDS', $ids);
    	
    	return $ids;
    }
    
    /**
     * Получить идентификаторы предложений размерного ряда.
     * @param $offerId идентификатор предолжения
     * @return array массив идентификаторов предложений входящих в размерный ряд.
     */
    public static function getComplectOfferIds($offerId)
    {
    	if(self::_cacheHas('COMPLECTS_BY_OFFER', $offerId)) {
    		return self::_cacheGet('COMPLECTS_BY_OFFER', array(), $offerId);
    	}
    	
    	$productId= HCatalog::getProductId($offerId);
    	
   		foreach (HCatalog::getOffers($productId) as $offer) {
			$offerIds[] = (int)$offer['ID'];
    	}
        
        if (!empty($offerIds)) {
        	foreach ($offerIds as $id) {
        		self::_cacheSet('COMPLECTS_BY_OFFER', $offerIds, $id);
        		self::_cacheSet('OFFER_PRODUCT_IDS', $productId, $id);
        	}
        }
        else {
        	self::_cacheSet('COMPLECTS_BY_OFFER', array(), $offerId);
        	self::_cacheSet('OFFER_PRODUCT_IDS', $productId, $offerId);
        }

        return $offerIds;
    }
    
    /**
     * Получить заголовок комплекта
     * @param integer $offerId идентификатор предложения
     */
    public static function getComplectTitle($offerId)
    {
    	$product=HCatalog::getProduct(HCatalog::getProductId($offerId));
    	
    	if(!empty($product)) {
    		return $product[0]['NAME'];
    	}
    	
    	return '';
    }
   
    /**
     * Получить все записи корзины
     * @param array $arSelectFields \CSaleBasket::GetList()::$arSelectFields
     * @param array $properties массив дополнительных свойств записи корзины, вида 
     * array(код_свойства, код_свойства_2)
     * @return array массив записей корзины (с дополнительным параметром "PROPERTIES")
     * вида array(идентификатор_записи_в_корзине=>массив_параметров)
     */
    public static function getBasketItems($arSelectFields=array(), $properties=array(), $refresh=false)
    {
     	$cacheHash=self::_getCacheHash([$arSelectFields, $properties]);
     	if(!$refresh && self::_cacheHas('BASKET_ITEMS', $cacheHash)) {
     		return self::_cacheGet('BASKET_ITEMS', array(), $cacheHash);
     	}
    	
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
       	
       	self::_cacheSet('BASKET_ITEMS', $items, $cacheHash);

        return $items;
    }
    
    /**
     * Получить цену основного товара.
     * @param integer $offerId идентификатор предложения.
     * @param integer|boolean $catalogGroupId идентификатор тип цены. Если передано TRUE, 
     * идентификатор типа цены будет получен методом Basket::getCatalogGroupId().
     * По умолчанию (FALSE) будет получен Basket::getDefaultCatalogGroupId().
     * @param boolean $isSizeComplect является комплектом размерного ряда
     * По умолчанию (FALSE) не является комплектом размерного ряда.
     * @return float
     */
    public static function getProductPrice($offerId, $catalogGroupId=false, $isSizeComplect=false)
    {
    	if(!$catalogGroupId) {
    		$catalogGroupId = self::getDefaultCatalogGroupId();
    	}
    	elseif($catalogGroupId === true) {
    		$catalogGroupId = self::getCatalogGroupId($offerId, $isSizeComplect);
    	}
    	
    	return HCatalog::getPrices($offerId, $catalogGroupId);
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
    	$productId = HCatalog::getProductId($offerId);
    	if (isset($basketData['COMPLECTS'][$productId])) {
    		foreach ($basketData['COMPLECTS'][$productId] as $item) {
    			$price = HCatalog::getPrices($item['PRODUCT_ID'], $catalogGroupId);
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
    		$price = HCatalog::getPrices($offerId, $catalogGroupId); 
   			$totalPrice = $price['PRICE'] * $basketData['SINGLES'][$offerId]['QUANTITY'];
    	}
    	 
    	return $totalPrice;
    }

    /**
     * Получить данные о кол-ве товаров в корзине
     * @param boolean $refresh получить данные с обновлением кэша.
     * @return array массив вида array(идентификатор_предложения => кол-во_данного_предложения_в_корзине)
     */
    public static function getBasketQuantities($refresh=false)
    {
    	if(!$refresh && !$catalogGroupId && self::_cacheHas('QUANTITIES')) {
    		return self::_cacheGet('QUANTITIES');
    	}
    	
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
        
        self::_cacheSet('QUANTITIES', $quantities);

        return $quantities;
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
    		$isSizeComplect = (H::aget($item, 'PROPERTIES.IS_SIZE_COMPLECT.VALUE', false) == 'Y');
    		if($isSizeComplect) {
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
     * Цены будут установлены относительно типа цены возвращаемой методом 
     * self::getDefaultCatalogGroupId().
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
    public static function getBasketData($refresh=false)
    {
    	if(!$refresh && self::_cacheHas('BASKET_DATA')) {
    		return self::_cacheGet('BASKET_DATA');
    	}
    	
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
    				$productId = HCatalog::getProductId($offerId);
    				if(!isset($complectItems[$productId])) {
    					$complectItems[$productId] = array();
    				}
    				
    				$complectItems[$productId][self::_getItemHash($offerId, true)] = array(
    					"PRODUCT_ID" => $offerId,
    					"QUANTITY" => $complectQuantity,
    					"PRICES" => self::getProductPrice($offerId),
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
	    					"PRICES" => self::getProductPrice($offerId),
	    					"PROPERTIES" => self::_generateBasketItemProperties($offerId, false)
	    				);
    				}
    			}
    		}
    	}
    	
    	$basketData = array(
    		"COMPLECTS"=>$complectItems,
    	 	"SINGLES"=>$singleItems
    	);
    	
    	self::_updateBasketData($basketData);
    	
    	return $basketData;
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
    		
    		$basketId = \Add2BasketByProductID(
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
    		$result['changed'] = ($result['changed'] || self::recalc($basketId));
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
    public static function recalc($basketId=false, $refresh=true)
    {
    	// если пользователь не является оптовым покупателем не выполняем никакого перерасчета
    	if (!\CSite::InGroup(self::$OPT_GROUPS)) {
    		return false;
    	}
    	
    	// перерасчет цен
    	$basketData = self::getBasketData($refresh);
    	foreach($basketData['COMPLECTS'] as $productId=>$items) {
    		foreach($items as $hash=>$item) {
    			$basketData['COMPLECTS'][$productId][$hash]['PRICES'] = self::getProductPrice($item['PRODUCT_ID'], true);
    		}
    	}
    	foreach($basketData['SINGLES'] as $offerId=>$item) {
   			$basketData['SINGLES'][$offerId]['PRICES'] = self::getProductPrice($offerId, true);
    	}
    	
    	// обновляем записи корзины
    	self::_updateBasketData($basketData);
    	
    	$changed = self::checkAndUpdateBasket();
    	
    	return $changed;
    }
    
    /**
     * Проверяет изменилась ли структура корзины, и если изменилась, 
     * то обновляет записи корзины.
     * @return boolean возвращает TRUE если структура корзины изменилась, 
     * или FALSE если структура корзины не изменилась.
     */
    public static function checkAndUpdateBasket($refresh=false, $update=true)
    {
    	$changed = false;
    	
    	$basketData = self::getBasketDataByBasket();
    	$newBasketData = self::getBasketData($refresh);
    	
    	if(count(H::aget($newBasketData, 'COMPLECTS', array())) != count(H::aget($basketData, 'COMPLECTS', array()))) {
    		$changed = true;
    	}
    	else {	    	
	    	foreach($newBasketData['COMPLECTS'] as $productId=>$items) {
	    		foreach($items as $hash=>$item) {
	    			if(!isset($basketData['COMPLECTS'][$productId][$hash])) {
	    				$changed = true;
	    				break;
	    			} 
	    			else {
	    				$price = H::aget($basketData['COMPLECTS'][$productId][$hash], 'PRICES.PRICE.PRICE', false);
	    				if(!$price || ($price != H::aget($item, 'PRICES.PRICE.PRICE', false))) {
	   						$changed = true;
	   						break;
	    				}
	    			}
	    		}
	    		
	   			if($changed) {
	    			break;
	    		}
	    	}
	    	
	    	if(!$changed) {
	    		foreach($basketData['SINGLES'] as $offerId=>$item) {
	    			$newPrice = H::aget($newBasketData['SINGLES'], $offerId.'.PRICES.PRICE.PRICE', false);
	    			if(empty($newPrice) || ($newPrice != $item['PRICE'])) {
	    				$changed = true;
	    				break;
	    			}
	    		}
	    		
	    		if(!$changed) {
	    			foreach($newBasketData['SINGLES'] as $offerId=>$item) {
	    				$newPrice = H::aget($item, 'PRICES.PRICE.PRICE', false);
		    			if(empty($newPrice) || ($newPrice != H::aget($basketData['SINGLES'], $offerId.'.PRICE'))) {
		    				$changed = true;
		    				break;
		    			}
	    			}
	    		}
	    	}
    	}
    	
    	if($changed && $update) {
    		// обновляем корзину
    		self::updateBasketByData();
    	}
    	
    	return $changed;
    }
    
    /**
     * Обновить записи корзины на основе массива со структурой
     * возвращаемой методом self::getBasketData().
     * @param array $basketData массив записей корзины со структурой
     * возвращаемой методом self::getBasketData()
     */
    public static function updateBasketByData($basketData=false)
    {
    	if(!$basketData) {
    		$basketData = self::getBasketData();
    	}
    	
    	self::clearBasket();
    	
    	foreach($basketData['COMPLECTS'] as $productId=>$items) {
    		foreach($items as $item) {
	    		$basketId = Add2BasketByProductID(
	    			$item['PRODUCT_ID'],
	    			$item['QUANTITY'],
	    			array(),
	    			self::_generateBasketItemProperties($item['PRODUCT_ID'], true)
	    		);
	    		
	    		\CSaleBasket::Update($basketId, array(
	    			'PRICE' => H::aget($item, 'PRICES.PRICE.PRICE', 0),
	    			'BASE_PRICE' => H::aget($item, 'PRICES.PRICE.PRICE', 0),
	    			'CALLBACK_FUNC' => '',
	    			//'ORDER_CALLBACK_FUNC' => '',
	    			'PRODUCT_PROVIDER_CLASS' => ''
	    		));
    		}
    	}
    	
    	foreach($basketData['SINGLES'] as $offerId=>$item) {
    		$basketId = Add2BasketByProductID(
    			$offerId,
    			$item['QUANTITY'],
    			array(),
    			self::_generateBasketItemProperties($offerId, false)
    		);
    		
    		\CSaleBasket::Update($basketId, array(
    			'PRICE' => H::aget($item, 'PRICES.PRICE.PRICE', 0),
    			'BASE_PRICE' => H::aget($item, 'PRICES.PRICE.PRICE', 0),
    			'CALLBACK_FUNC' => '',
    			//'ORDER_CALLBACK_FUNC' => '',
    			'PRODUCT_PROVIDER_CLASS' => ''
    		));
    	}
    	
    	return true;
    }
    
    /**
     * Генерация дополниьтельных свойств записи корзины.
     * @access protected 
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
		        "VALUE" => HCatalog::getProductId($offerId),
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
     * Обновить значение кэша BASKET_DATA.
     * @access protected
     * @param array $basketData массив записей корзины со структурой
     * возвращаемой методом self::getBasketData()
     */
    protected static function _updateBasketData($basketData)
    {
    	self::_cacheSet('BASKET_DATA', $basketData);
    }
    
    /**
     * Получить (сгенерировать) хэш записи в корзине
     * @access protected
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
     * @access protected
     * @param string $data данные для генерации хэша кэша.
     */
    protected static function _getCacheHash($data=false)
    {
    	return md5(serialize($data));
    }

    /**
     * Получить значение кэша
     * @access protected
     * @param string $id идентификатор кэша.
     * @param mixed $default значение по умолчанию.
     * @param string $secondaryId дополнительный идентификатор кэша.
     * По умолчанию (FALSE) не задан.
     * @return mixed
     */
    protected static function _cacheGet($id, $default=false, $secondaryId=false)
    {
    	if(self::_cacheHas($id, $secondaryId)) {
    		if($secondaryId) {
    			return self::$_cache[$id][$secondaryId];
    		}
    		return self::$_cache[$id];
    	}
    	else {
    		return $default;
    	}
    }
    
    /**
     * Проверка существования значения в кэше
     * @access protected
     * @param string $id идентификатор кэша.
     * @param string $secondaryId дополнительный идентификатор кэша.
     * По умолчанию (FALSE) не задан.
     * @return boolean
     */
    protected static function _cacheHas($id, $secondaryId=false)
    {
    	if($secondaryId) {
    		return isset(self::$_cache[$id][$secondaryId]);
    	}
    	 
    	return isset(self::$_cache[$id]);
    }
    
    /**
     * Установить значение в кэш
     * @access protected
     * @param string $id идентификатор кэша.
     * @param mixed $value значение
     * @param string $secondaryId дополнительный идентификатор кэша.
     * По умолчанию (FALSE) не задан.
     */
    protected static function _cacheSet($id, $value, $secondaryId=false)
    {
    	if($secondaryId) {
    		self::$_cache[$id][$secondaryId] = $value;
    	}
    	else {
    		self::$_cache[$id] = $value;
    	}
    }
    
    /**
     * Очистить переменную кэша
     * @access protected
     * @param string $id идентификатор кэша.
     */
    protected static function _cacheClear($id)
    {
    	if(isset(self::$_cache[$id])) {
    		unset(self::$_cache[$id]);
    	}
    }
}