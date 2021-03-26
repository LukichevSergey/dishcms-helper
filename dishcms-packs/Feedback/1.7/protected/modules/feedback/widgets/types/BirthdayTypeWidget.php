<?php
/**
 * Birthday type widget
 *
 */
namespace feedback\widgets\types;

class BirthdayTypeWidget extends BaseTypeWidget
{
	/**
	 * (non-PHPdoc)
	 * @see \feedback\widgets\types\BaseTypeWidget::run()
	 */
	public function run($name, \feedback\components\FeedbackFactory $factory, \CActiveForm $form)
	{
		$this->render('birthday', compact('name', 'factory', 'form'));
	}
}