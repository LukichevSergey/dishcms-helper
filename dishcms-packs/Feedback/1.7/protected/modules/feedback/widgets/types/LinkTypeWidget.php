<?php
/**
 * Link type widget
 *
 */
namespace feedback\widgets\types;

class LinkTypeWidget extends BaseTypeWidget
{
	/**
	 * (non-PHPdoc)
	 * @see \feedback\widgets\types\BaseTypeWidget::run()
	 */
	public function run($name, \feedback\components\FeedbackFactory $factory, \CActiveForm $form)
	{
		$this->render('link', compact('name', 'factory', 'form'));
	}
}