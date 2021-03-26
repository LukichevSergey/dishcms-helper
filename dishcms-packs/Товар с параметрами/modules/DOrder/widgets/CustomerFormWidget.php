<?php
/**
 * Виджет формы покупателя.
 * 
 * @use \YiiHelper (>=1.02)
 */
namespace DOrder\widgets;

use \DOrder\models\CustomerForm;

class CustomerFormWidget extends BaseWidget
{
	/**
	 * Модель
	 * @var \DOrder\models\CustomerForm
	 */
	public $model;
	
	/**
	 * Заголовок кнопки отправки формы
	 * @var string
	 */
	public $submitTitle = 'Оформить заказ';
	 
	/**
	 * (non-PHPdoc)
	 * @see CWidget::run()
	 */
	public function run()
	{
		if(!($this->model instanceof CustomerForm)) 
			throw new \Exception('DOrder.CustomerFormWidget: model not instance of \DOrder\models\CustomerForm.');

// 		$this->model->attributes = \Yii::app()->request->getPost(\YiiHelper::slash2_($this->model));
		
		$this->render('customer_form');
	}
} 