<?php
/**
 * Text type widget
 *
 */
namespace feedback\widgets\types;

class TextTypeWidget extends BaseTypeWidget
{
	/**
	 * (non-PHPdoc)
	 * @see \feedback\widgets\types\BaseTypeWidget::run()
	 */
	public function run($name, \feedback\components\FeedbackFactory $factory, \CActiveForm $form)
	{
		$this->render('text', compact('name', 'factory', 'form'));
	}
}