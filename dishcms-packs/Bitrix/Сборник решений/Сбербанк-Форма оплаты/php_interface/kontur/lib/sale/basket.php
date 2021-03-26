<?
/**
 * Basket
 *
 * @link https://mrcappuccino.ru/blog/post/work-with-basket-bitrix-d7
 */
namespace Kontur\Core\Sale;

\Bitrix\Main\Loader::includeModule('sale');

class Basket 
{
    /**
     * Получение корзины для текущего пользователя.
     */
    public static function getBasket()
    {
        return \Bitrix\Sale\Basket::loadItemsForFUser(\Bitrix\Sale\Fuser::getId(), \Bitrix\Main\Context::getCurrent()->getSite());
    }
    
    /**
     * Добавление товара в корзину
     * @param integer $id идентификатор товара.
     * @param integer $quantity кол-во добавляемого товара. 
     * По умолчанию 1 (один).
     */
    public static function add($id, $quantity=1)
    {
        $basket = self::getBasket();

        if(!is_numeric($quantity) || ((int)$quantity < 1)) {
        	return false;
        }
        
        if ($item = $basket->getExistsItem('catalog', $id)) {
            $item->setField('QUANTITY', $item->getQuantity() + $quantity);
        }
        else {
            $item = $basket->createItem('catalog', $id);
            $item->setFields(array(
                'QUANTITY' => $quantity,
                'CURRENCY' => \Bitrix\Currency\CurrencyManager::getBaseCurrency(),
                'LID' => \Bitrix\Main\Context::getCurrent()->getSite(),
                'PRODUCT_PROVIDER_CLASS' => 'CCatalogProductProvider',
            ));
        }
        
        return $basket->save();
    }
    
    /**
     * Удаление товара из корзины.
     */
    public static function delete($id)
    {
        $basket = self::getBasket();
        
        $basket->getItemById($id)->delete();
        
        return $basket->save();
    }
    
    /**
     * Получение кол-ва товара в корзине
     * @param \Bitrix\Sale\Basket|NULL объект корзины. 
     * По умолчанию (NULL) - будет получен self::getBasket().
     */
    public static function getTotalCount($basket=null)
    {
        if(!$basket) {
            $basket = self::getBasket();
        }
        
        $count=0;
        foreach($basket->getOrderableItems() as $item) {
        	$count += $item->getField('QUANTITY');
        }

        return $count;
        //return array_sum($basket->getQuantityList());
    }
    
    /**
     * Получение кол-ва позиций в заказе
     * @param \Bitrix\Sale\Basket|NULL объект корзины. 
     * По умолчанию (NULL) - будет получен self::getBasket().
     */
    public static function getItemsCount($basket=null)
    {
        if(!$basket) {
            $basket = self::getBasket();
        }
        
        return count($basket->getQuantityList());
    }
}