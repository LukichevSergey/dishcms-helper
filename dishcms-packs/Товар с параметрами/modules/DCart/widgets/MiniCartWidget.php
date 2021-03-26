<?php
/**
 * Виджет мини-корзины
 * 
 * @use \AssetHelper
 */
namespace DCart\widgets;

class MiniCartWidget extends BaseCartWidget
{
	/**
	 * Ссылка на страницу оформления заказа.
	 * @var string|array
	 */
	public $orderUrl = '/order';
	
	/**
	 * (non-PHPdoc)
	 * @see \CWidget::init()
	 */
	public function init()
	{
		parent::init();
		
		\AssetHelper::publish(array(
			'path' => __DIR__ . DIRECTORY_SEPARATOR . 'assets',
			'js' => array('js/classes/DCartMiniWidget.js', 'js/dcart_mini_cart_widget.js'),
			'css' => 'css/mini_cart.css'
		));
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \CWidget::run()
	 */
	public function run()
	{
		$cart = \Yii::app()->cart;
		
		$this->render('mini_cart_default', compact('cart'));
	}
	
	/**
	 * Render mini-cart summary
	 * @param boolean $return Возвращать контент или выводить в поток.
	 * @return string|void
	 */
	public static function renderSummary($return=false)
	{
		$widget = new self;
		$cart = \Yii::app()->cart;
		
		return $widget->render('_mini_cart_summary', compact('cart'), $return);
	} 
	
	/**
	 * Render mini-cart items
	 * @param boolean $return Возвращать контент или выводить в поток.
	 * @return string|void
	 */
	public static function renderItems($return=false)
	{
		$widget = new self;
		$cart = \Yii::app()->cart;
	
		return $widget->render('_mini_cart_items', compact('cart'), $return);
	}
}