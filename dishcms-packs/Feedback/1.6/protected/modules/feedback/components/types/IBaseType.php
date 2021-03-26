<?php
namespace feedback\components\types;

interface IBaseType 
{
	/**
	 * Rules
	 * @see \CActiveRecord::rules()
	 * @return array
	 */
	public function rules();
	
	/**
	 * Get SQL column definition of this type. 
	 */
	public function getSqlColumnDefinition();
	
	/**
	 * Нормализация значения
	 * @param mixed $value attribute value.
	 * @return mixed normalized attribute value.
	 */
	public function normalize($value);
	
	/**
	 * Форматирование значения для отображения
	 * @param mixed $value attribute value.
	 * @return mixed formatted attribute value.
	 */
	public function format($value);
	
	/**
	 * Получение класса виджета
	 * @see \feedback\widgets\FeedbackWidget
	 * @return FeedbackWidget widget object based on FeedbackWidget.
	 */
	public function getWidget();
	
	/**
	 * Create and run type widget
	 *
	 * @param \feedback\components\FeedbackFactory $factory feedback factory object.
	 * @param \CActiveForm $form form.
	 * @param array $params widget parameters (@see needly type-widget documentation).
	 */
	public function widget(\feedback\components\FeedbackFactory $factory, \CActiveForm $form, $params=array());
}