<?php
/**
 * Базовый класс для виджетов корзины
 * 
 * @use \AssetHelper
 */
namespace DCart\widgets;

abstract class BaseCartWidget extends \CWidget
{
	/**
	 * (non-PHPdoc)
	 * @see \CWidget::init()
	 */
	public function init()
	{
		\AssetHelper::publish(array(
			'path' => __DIR__ . DIRECTORY_SEPARATOR . 'assets',
			'js' => array(
				'js/classes/DCart.js',
				'js/dcart_helpers.js',
				'js/phpjs/json_decode.js',
				'/js/jquery/jquery-impromptu.3.2.min.js',
				'/js/jquery/jquery.debounce-1.0.5.js'
			),
			'css' => 'css/style.css'
		));
	}	
} 