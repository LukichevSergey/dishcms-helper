<?php
namespace kontur;

use Bitrix\Main\Loader;

Loader::includeModule("sale");
Loader::includeModule("catalog");

class Cart
{
	/**
	 * @var \sovamama\NewBasket|null экземпляр класса
	 */
	private static $_instance=null;

	/**
	 * Получить статический экземпляр класса
	 * @return \sovamama\Basket
	 */
	public static function getInstance()
	{
		if(static::$_instance === null) {
			static::$_instance = new self();
		}
		
		return static::$_instance;
	}

	/**
	 * @var array кэш
	 */
	private static $_cache=[];
	
	/**
	 * @var \Bitrix\Sale\Basket
	 */
	private static $_basket=null;

	/**
	 * Получить объект корзины текущего пользователя
	 * @param boolean $refresh обновить корзину
	 * @return &\Bitrix\Sale\Basket
	 */
	public function &getBasket($refresh=false)
	{
		if($refresh || !static::$_basket) {
			static::$_basket=\Bitrix\Sale\Basket::loadItemsForFUser(\Bitrix\Sale\Fuser::getId(), \Bitrix\Main\Context::getCurrent()->getSite());
		}
		
		return static::$_basket;
	}
	
	/**
	 * Получить записи корзины текущего пользователя
	 * @return array
	 */
	public function getBasketItems()
	{
		return $this->getBasket()->getBasketItems();
	}
	
	/**
	 * Получение общей суммы товаров в корзину
	 * @return integer
	 */
	public function getTotalPrice()
	{
		$totalPrice=0;
		
		foreach($this->getBasketItems() as $basketItem) {
			if($basketItem->canBuy()) {
				$totalPrice+=$basketItem->getPrice() * $basketItem->getQuantity();
			}
		}
		
		return $totalPrice;
	}
	
	/**
	 * Получение общего кол-ва товаров в корзине
	 * @return integer
	 */
	public function getTotalQuantity()
	{
		$totalQuantity=0;
		
		foreach($this->getBasketItems() as $basketItem) {
			if($basketItem->canBuy()) {
				$totalQuantity+=$basketItem->getQuantity();
			}
		}
		
		return $totalQuantity;
	}
}
