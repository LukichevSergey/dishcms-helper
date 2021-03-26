<?php
/**
 * Base type widget
 * 
 */
namespace feedback\widgets\types;

class BaseTypeWidget extends \CWidget implements IBaseTypeWidget
{
	/**
	 * Widget parameters
	 * @var array
	 */
	public $params = array();
	
	/**
	 * (non-PHPdoc)
	 * @see \feedback\widgets\types\IBaseTypeWidget::run()
	 */
	public function run($name, \feedback\components\FeedbackFactory $factory, \CActiveForm $form)
	{
		
	}
}