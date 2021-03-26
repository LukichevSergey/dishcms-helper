<?php
/**
 * Виджет корзины
 * 
 * @use \AssetHelper
 */
namespace DCart\widgets;

class CartWidget extends BaseCartWidget
{
	/**
	 * (non-PHPdoc)
	 * @see \CWidget::init()
	 */
	public function init()
	{
		parent::init();
	
		\AssetHelper::publish(array(
			'path' => __DIR__ . DIRECTORY_SEPARATOR . 'assets',
			'js' => array('js/classes/DCartWidget.js', 'js/dcart_cart_widget.js'),
			'css' => 'css/cart.css',
		));
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \CWidget::run()
	 */
	public function run()
	{
		$cart = \Yii::app()->cart;
		
		$this->render('cart_default', compact('cart'));
	}
	
	/**
	 * Render cart items
	 * @param boolean $return Возвращать контент или выводить в поток.
	 * @return string|void
	 */
	public static function renderItems($return=false)
	{
		$widget = new self;
		$cart = \Yii::app()->cart;
	
		return $widget->render('_cart_items', compact('cart'), $return);
	}
}