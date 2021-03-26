<?php
/**
 * Helper
 * Count field widget 
 * 
 * Аттрибут "name" устанавливается в значение "count".
 * 
 */
namespace DCart\widgets\helpers;

class CountFieldWidget extends \CWidget 
{
	/**
	 * Id поля
	 * @var string
	 */
	public $id;
	
	/**
	 * Заголовок поля
 	 * @var string
	 */
	public $label = 'Кол-во';
	
	/**
	 * (non-PHPdoc)
	 * @see CWidget::run()
	 */
	public function run()
	{
		$this->render('count_field');
	}
}