<?php
/**
 * Yii Helper
 * 
 * @version 1.04
 * 
 * @history:
 * 1.01: Add attributeExists() method.
 * 1.02: Object type now is available for $className parameter of slash2_() method.
 * 1.03: Add formatDate() method.
 * 1.04: Add arraySort() method.
 */
class YiiHelper extends \CComponent
{
	/**
	 * Replace namespace "\" char to "_".
	 * @param string|object $className
	 * @return mixed
	 */
	public static function slash2_($className) 
	{
		if(is_object($className)) $className = get_class($className);
		
		return preg_replace('/\\\\+/', '_', trim($className, '\\'));
	}
	
	/**
	 * Вырезать namespace
	 * @param string $className class name.
	 * @return string
	 */
	public static function cutNamespace($className)
	{
		return preg_replace('/.*?([^\\\\]+)$/', '\\1', $className);
	}
	
	/**
	 * Get class name
	 * @param object|string $className object or class name.
	 * @param string $withoutNamespace return without namespace.
	 * @return string
	 */
	public static function getClassName($className, $withoutNamespace=false)
	{
		$pattern = '/^' . ($withoutNamespace ? '.*?(' : '(.*?') . '[^\\\\]+)$/';
		return preg_replace($pattern, '\\1', (is_object($className) ? get_class($className) : $className));
	}
	
	/**
	 * Проверка существования аттрибута у модели.
	 * Актуально для моделей \CActiveRecord, т.к. property_exists возвращает 
	 * false на явно не объявленные свойства. 
	 * @param object $model модель.
	 * @param string $attribute аттрибут.
	 * @return boolean
	 */
	public static function attributeExists($model, $attribute)
	{
		try {
			$value = $model->$attribute;
			return true;
		}
		catch(\Exception $e) {
			return false;
		}
	}
	
	/**
	 * Получить время
	 * @param integer|string $time timestamp время.
	 * @param string $pattern шаблон отображения.
	 * @return string
	 */
	public static function formatDate($time, $pattern='dd.MM.yyyy HH:mm')
	{
		return \Yii::app()->dateFormatter->format($pattern, $time);
	}
	
	/**
	 * Сортировка массива
	 * Сортировка массива по указанному порядку в массиве ключей
	 * Не возвращаются все ключи не входящие в $orderedKeys 
	 * @param array $array сортируемый массив
	 * @param array $orderedKeys массив упорядоченных ключей для сортировки 
	 * @return array отсортированный и отфильтрованный массив.
	 */
	public static function arraySort($array, $orderedKeys=array())
	{
		$result = array();
		foreach($orderedKeys as $key) {
			if(isset($array[$key])) $result[$key] = $array[$key];
		}
		return $result;
	}
}