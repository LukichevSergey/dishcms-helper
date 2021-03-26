<?php
/**
 * Email type widget
 *
 */
namespace feedback\widgets\types;

class EmailTypeWidget extends BaseTypeWidget
{
	/**
	 * (non-PHPdoc)
	 * @see \feedback\widgets\types\BaseTypeWidget::run()
	 */
	public function run($name, \feedback\components\FeedbackFactory $factory, \CActiveForm $form)
	{
		$this->render('email', compact('name', 'factory', 'form'));
	}
}