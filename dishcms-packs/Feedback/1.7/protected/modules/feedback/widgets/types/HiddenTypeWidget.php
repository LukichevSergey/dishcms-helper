<?php
/**
 * Hidden type widget
 *
 */
namespace feedback\widgets\types;

class HiddenTypeWidget extends BaseTypeWidget
{
	/**
	 * (non-PHPdoc)
	 * @see \feedback\widgets\types\BaseTypeWidget::run()
	 */
	public function run($name, \feedback\components\FeedbackFactory $factory, \CActiveForm $form)
	{
		$this->render('hidden', compact('name', 'factory', 'form'));
	}
}