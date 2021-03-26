<?php
/**
 * Interface for types widget
 */
namespace feedback\widgets\types;

interface IBaseTypeWidget 
{
	/**
	 * (non-PHPdoc)
	 * @see CWidget::run()
	 *
	 * @param string $name attribute name.
	 * @param \feedback\components\FeedbackFactory $factory feedback factory.
	 * @param \CActiveForm $form form.
	 */
	public function run($name, \feedback\components\FeedbackFactory $factory, \CActiveForm $form);
}