<?
/**
 * 
 */
namespace kontur;

class Catalog
{
    // const IBLOCK_ID=16;
    
    private static $cacheDiscountPrices=array();
    
    /**
     * Получение минимальной цены при применении скидок
     * @var integer $productId идентификатор товара
     * @var integer $basketTotal сумма корзины, для которой определяется скидка
     * @return integer
     */
    public static function getMinDiscountPrice($productId, $basketTotal=0)
    {
        $result=static::getMinDiscountPrices(array($productId), $basketTotal);
        
        return $result[$productId];
    }
    
    /**
     * Получение минимальной цены при применении скидок
     * @var array $productIDs массив идентификаторов товаров
     * @var integer $basketTotal сумма корзины, для которой определяется скидка
     * @return array массив вида array(идентификатор_товара=>цена_со_скидкой)
     */
    public static function getMinDiscountPrices($productIDs, $basketTotal=0)
    {
        // error_reporting(E_ALL);ini_set('display_errors', 1);
        $prices=array();
        
        $basePrices=array();
        $basket = \Bitrix\Sale\Basket::create(SITE_ID);
        /* $itemsProps=array();
        if(!empty($productIDs)) {
            $rs=\CIBlockElement::GetList(['SORT'=>'ASC'], ['ID'=>$productIDs, 'IBLOCK_ID'=>self::IBLOCK_ID], false, false, ['ID', 'IBLOCK_ID', 'IBLOCK_SECTION_ID', 'PROPERTY_*']);
            while($ob = $rs->GetNextElement()) {
                $fields = $ob->GetFields();
                $id=$fields['ID'];                
                $itemsProps[]=[];
                
                $itemsProps[$id][]=[                    
                   'NAME' => 'Section',
                   'CODE' => 'SECTION_ID',
                   'VALUE' => is_array($fields['IBLOCK_SECTION_ID']['VALUE']) ? $fields['IBLOCK_SECTION_ID']['VALUE'][0] : $fields['IBLOCK_SECTION_ID']['VALUE']
                ];
                
                $props = $ob->GetProperties();
                foreach($props as $prop) {
                    $itemsProps[$id][]=[
                       'NAME' => $prop['NAME'],
                       'CODE' => $prop['CODE'],
                       'VALUE' => $prop['VALUE']
                    ];
                }
            }
        }
        */
        foreach($productIDs as $productId) {
            if(isset(static::$cacheDiscountPrices[$basketTotal]) && array_key_exists($productId, static::$cacheDiscountPrices[$basketTotal])) {
                $prices[$productId]=static::$cacheDiscountPrices[$basketTotal][$productId];
            } 
            else {
                $prices[$productId]=null;
                static::$cacheDiscountPrices[$basketTotal][$productId]=null;
                
                $item = $basket->createItem('catalog', $productId);      
                $item->setFields([
                    'QUANTITY' => 1,
                    'CURRENCY' => \Bitrix\Currency\CurrencyManager::getBaseCurrency(),
                    'LID' => \Bitrix\Main\Context::getCurrent()->getSite(),
                    'PRODUCT_PROVIDER_CLASS' => 'CCatalogProductProvider',
                    'CAN_BUY'=>'Y'
                ]);
                
                $price=$item->getPrice();                
                if(($price < 0) || ($price > 10000)) {
                    $item->delete();
                }
                else {
                    $basePrices[$productId]=$price;
                    $item->setField('QUANTITY', (ceil($basketTotal / $price) ?: 1) + 1);
                    
                    //$basketPropertyCollection = $item->getPropertyCollection();
                    //$basketPropertyCollection->setProperty($itemsProps[$productId]);
                }
                // $basket->refreshData( array ( 'PRICE' , 'COUPONS' ));
                // var_dump($item->getPrice(), $item->getField('QUANTITY'));
            }
        }
        
        $items = $basket->getBasketItems();
        if(!empty($items)) {
            // $discount = \Bitrix\Sale\Discount::loadByBasket( $basket );
            global $USER;
            $discount = \Bitrix\Sale\Discount::buildFromBasket($basket, new \Bitrix\Sale\Discount\Context\UserGroup($USER->GetUserGroupArray()));
            $discount->calculate();
            $result = $discount->getApplyResult(true);
            /* if(true) {
                $order = \Bitrix\Sale\Order::create(SITE_ID);
                $order->setField('RECURRING_ID', 1);
                $order->setBasket($basket);
                $discount = $order->getDiscount();  
            }
            else {
                $discount = \Bitrix\Sale\Discount::buildFromBasket($basket, new \Bitrix\Sale\Discount\Context\Fuser($basket->getFUserId(true)));
            }
            // $discount->setExecuteModuleFilter(['all', 'catalog']);
            $discount->calculate();
            $result = $discount->getApplyResult(true);
            */
            $discounts=array();            
            foreach($items as $item) {
                $productId = $item->getProductId();
                $basketCode = $item->getBasketCode();
                if (!empty($result['PRICES']['BASKET'][$basketCode]['PRICE'])) {
                    $prices[$productId]=null;
                    if($basePrices[$productId] != $result['PRICES']['BASKET'][$basketCode]['PRICE']) {
                        $prices[$productId]=$result['PRICES']['BASKET'][$basketCode]['PRICE'];
                    }
					static::$cacheDiscountPrices[$basketTotal][$productId]=$prices[$productId];
                }
                elseif (!empty($result['RESULT']['BASKET'])) {
                    foreach($result['RESULT']['BASKET'] as $basketDiscounts) {
                         foreach($basketDiscounts as $discount) {
                            if($discount['APPLY'] == 'Y') {
                                $discountList=$result['DISCOUNT_LIST'][$discount['DISCOUNT_ID']]['ACTIONS_DESCR_DATA']['BASKET'];
                                $discounts[$productId]=array();
                                foreach($discountList as $discountItem) {
                                    $discounts[$productId][]=array(
                                        'VALUE_TYPE'=>$discountItem['VALUE_TYPE'],
                                        'VALUE'=>$discountItem['VALUE'],
                                        'CURRENCY'=>\Bitrix\Currency\CurrencyManager::getBaseCurrency(),
                                        'MAX_DISCOUNT'=>$discountItem['LIMIT_VALUE']
                                    );
                                }
                            }
                        }
                    }
                }
            }
            
            if(!empty($discounts)) {
                foreach($discounts as $productId=>$productDiscounts) {
                     $prices[$productId]=\CCatalogProduct::CountPriceWithDiscount(
                        $basePrices[$productId], 
                        \Bitrix\Currency\CurrencyManager::getBaseCurrency(),
                        $productDiscounts
                    );
                    
                    if($prices[$productId] === $basePrices[$productId]) {
                        $prices[$productId]=null;
                    }
                    static::$cacheDiscountPrices[$basketTotal][$productId]=$prices[$productId];
                }
            }
        }
        
        return $prices;
    }
    
    public static function setSectionItemsDiscountPrices(&$arResult)
    {
        $productIDs=array();        
        
        foreach ($arResult["ITEMS"] as $item) {
            $productIDs[]=$item['ID'];
        }
        
        if(!empty($productIDs)) {
            $arResult['ITEMS_DISCOUNT_PRICES']=static::getMinDiscountPrices($productIDs, 10001);
        }
    }
}
