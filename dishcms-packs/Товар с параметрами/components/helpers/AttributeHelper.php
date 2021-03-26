<?php
/**
 * Attribute Helper
 * 
 * @version 1.0
 */
class AttributeHelper extends CComponent
{
	/**
	 * Get attribute value.
	 * 
	 * @param array $attributes attributes array.
	 * @param string $name attribute name.
	 * @param string $default default value, if attribute not exists.
	 * @return Ambigous <string, unknown>
	 */
	public static function get($attributes, $name, $default=null)
	{
		return isset($attributes[$name]) ? $attributes[$name] : $default;
	}	
	
	/**
	 * Получение обязательного аттрибута
	 * 
	 * Если не найдено атрибута с переданным именем, 
	 * бросается исключение типа \AttributeHelperException
	 * 
	 * @param array $attributes attributes array.
	 * @param string $name attribute name.
	 * @return mixed attribute value.
	 */
	public static function getR($attributes, $name)
	{
		if(!isset($attributes[$name])) 
			throw new \AttributeHelperException("Attribute \"{$name}\" not found.");
		
		return $attributes[$name];
	}
}

/**
 * Attribute helper exception class.
 *
 * @see \Exception
 * 
 */
class AttributeHelperException extends \Exception
{	
}