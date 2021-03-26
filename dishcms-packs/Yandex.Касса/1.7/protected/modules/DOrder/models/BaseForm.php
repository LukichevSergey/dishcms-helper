<?php
/**
 * Базовый класс для моделей формы покупателя
 *
 */
namespace DOrder\models;

use \AttributeHelper as A;

class BaseForm extends \CFormModel
{
	/**
	 * Instance of this model
	 * @var object
	 */
	private static $_instance;
	 
	/**
	 * @param string $className form model class name.
	 * @return CustomerForm the static model class
	 */
	public static function model($className=__CLASS__)
	{
		if(!(self::$_instance instanceof $className)) {
			self::$_instance = new $className(); 
		}
		
		return self::$_instance;
	}
	
	/**
	 * Get payment types
	 * @return array (type=>title)
	 */
	public function getPaymentTypes()
	{
		return array();
	}
	
	/**
	 * Получить аттрибуты
	 * @see CModel::getAttributes()
	 * @param mixed $names
	 * @param boolean $returnALV Возвращать результат в виде массива 
	 * array(attribute=>array('label' => label, 'value' => value)), или 
	 * в виде простого array(name=>value)
	 * @param boolean $serialize Сериализовать результат или нет.
	 * @return array|string возвращается строка, если параметр $serialize установлен в true.
	 */
	public function getAttributes($names=null, $returnALV=false, $serialize=false) 
	{
		$attributes = parent::getAttributes($names); 
		
		if($returnALV) {
			$labels = $this->attributeLabels();
			foreach($attributes as $name=>$value) {
				$attributes[$name] = array('label' => A::get($labels, $name, $name), 'value' => $value);
			}	
		}

		return $serialize ? serialize($attributes) : $attributes;
	}
	
}