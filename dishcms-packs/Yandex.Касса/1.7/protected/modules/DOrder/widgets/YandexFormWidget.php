<?php
/**
 * Виджет формы покупателя.
 * 
 * @use \YiiHelper (>=1.02)
 */
namespace DOrder\widgets;

use \DOrder\models\YandexForm;

class YandexFormWidget extends BaseWidget
{
	/**
	 * Yandex parameter "scid".
	 * @var string
	 */
	public $scid = null;
	
	/**
	 * Yandex parameter "ShopID". 
	 */
	public $ShopID = null;
	
	/**
	 * Модель
	 * @var \DOrder\models\YandexForm
	 */
	public $model;
	
	/**
	 * Заголовок кнопки отправки формы
	 * @var string
	 */
	public $submitTitle = 'Оплатить';
	
	public $submitCssClass = 'yandex-submit-btn';
	
	private $_action = 'https://money.yandex.ru/eshop.xml';
	 
	/**
	 * (non-PHPdoc)
	 * @see CWidget::init()
	 */
	public function init()
	{
		if(!($this->model instanceof YandexForm))
			throw new \Exception('DOrder.YandexFormWidget: model not instance of \DOrder\models\YandexForm.');
		
		$this->model->scid = $this->scid;
		$this->model->ShopID = $this->ShopID;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CWidget::run()
	 */
	public function run()
	{
		if(!($this->model instanceof YandexForm)) 
			throw new \Exception('DOrder.YandexFormWidget: model not instance of \DOrder\models\YandexForm.');

// 		$this->model->attributes = \Yii::app()->request->getPost(\YiiHelper::slash2_($this->model));
		
		$this->render('yandex_form');
	}
} 