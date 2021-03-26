<?php
/**
 * String type widget
 *
 */
namespace feedback\widgets\types;

class StringTypeWidget extends BaseTypeWidget
{
	/**
	 * (non-PHPdoc)
	 * @see \feedback\widgets\types\BaseTypeWidget::run()
	 */
	public function run($name, \feedback\components\FeedbackFactory $factory, \CActiveForm $form)
	{
		$this->render('string', compact('name', 'factory', 'form'));
	}
}