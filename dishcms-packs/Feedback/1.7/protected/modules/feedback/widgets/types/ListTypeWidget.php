<?php
/**
 * List type widget
 *
 * params: ('selected'=><selected value>)
 */
namespace feedback\widgets\types;

use \AttributeHelper as A;

class ListTypeWidget extends BaseTypeWidget
{
	/**
	 * List items (value=>display)
	 * @var array
	 */
	public $items = array();
	
	/**
	 * (non-PHPdoc)
	 * @see \feedback\widgets\types\BaseTypeWidget::run()
	 */
	public function run($name, \feedback\components\FeedbackFactory $factory, \CActiveForm $form)
	{
		reset($this->items);
		$this->params['selected'] = A::get($this->params, 'selected', key($this->items));
		$this->items = $factory->getModelFactory()->getAttribute($name)->getModel()->items;
		$this->render('list', compact('name', 'factory', 'form'));
	}
}