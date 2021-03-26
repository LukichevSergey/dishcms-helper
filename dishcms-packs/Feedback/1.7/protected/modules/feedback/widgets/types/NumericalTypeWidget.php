<?php
/**
 * Numerical type widget
 *
 */
namespace feedback\widgets\types;

class NumericalTypeWidget extends BaseTypeWidget
{
	/**
	 * (non-PHPdoc)
	 * @see \feedback\widgets\types\BaseTypeWidget::run()
	 */
	public function run($name, \feedback\components\FeedbackFactory $factory, \CActiveForm $form)
	{
		$this->render('numerical', compact('name', 'factory', 'form'));
	}
}