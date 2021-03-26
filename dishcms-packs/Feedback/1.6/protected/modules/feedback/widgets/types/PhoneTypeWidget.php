<?php
/**
 * Phone type widget
 *
 */
namespace feedback\widgets\types;

class PhoneTypeWidget extends BaseTypeWidget
{
	/**
	 * (non-PHPdoc)
	 * @see \feedback\widgets\types\BaseTypeWidget::run()
	 */
	public function run($name, \feedback\components\FeedbackFactory $factory, \CActiveForm $form)
	{
		$this->render('phone', compact('name', 'factory', 'form'));
	}
}