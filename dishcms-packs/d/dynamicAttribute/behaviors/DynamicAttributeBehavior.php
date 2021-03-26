<?php
/**
 * Dynamic attribute behavior
 * PHP >=5.4
 *
 * После подключения поведения, добавить в модель также трэйт
 * use ext\D\dynamicAttribute\runtime\traits\<имя внешней модели>_<Имя аттрибута в CamelCase>;
 * В имени внешней модели NS заменяются на "_".
 * пример для модели с namespace "\models\Product" и аттрибута "extend_props":
 * use ext\D\dynamicAttribute\runtime\traits\models_Product_ExtendProps;
 */
namespace ext\D\dynamicAttribute\behaviors;

use \AttributeHelper as A;

class DynamicAttributeBehavior extends \CBehavior
{
	/**
	 * Dynamic attribute 
	 * @var string
	 */
	public $attribute = 'extend_props';
	
	/**
	 * Разрешены пустые записи или нет.
	 * @var boolean
	 */
	public $allowEmpty = false;
	
	/**
	 * Опция безопасного получения значения. 
	 * Если значение будет битым, то возвращается пустой массив.
	 * @var boolean
	 */
	public $safeGet = true;
	
	/**
	 * @var boolean добавить в таблицу модели поле для хранения данных атрибута.
	 */
	public $addColumn = true;
	
	/**
 	 * @var string имя события, на которое срабатывает вызов метода 
 	 * DynamicAttributeBehavior::beforeSave().
 	 * Например для подключения к \CFormModel можно задать "onBeforeValidate" 
	 */
	public $eventOnBeforeSave='onBeforeSave';
	
	/**
	 * (non-PHPdoc)
	 * @see CBehavior::events()
	 */
	public function events()
	{
		return array(
			$this->eventOnBeforeSave=>'beforeSave'
		);		
	}
	
	/**
	 * (non-PHPDoc)
	 * @see CBehavior::attach()
	 */
	public function attach($owner)
	{
		parent::attach($owner);
		
		if($this->addColumn && !$this->owner->getTableSchema()->getColumn($this->attribute)) {
			$this->owner->getDbConnection()->createCommand()->addColumn(
				$this->owner->tableName(),
				$this->attribute,
				'TEXT'
			);
			$this->owner->refreshMetaData();
		}
		
		if(!$this->owner->{$this->attribute}) 
			$this->owner->{$this->attribute} = array();
	}
	
	/**
	 * Before save
	 * @return boolean
	 */
	public function beforeSave()
	{ 
		return $this->set($this->owner->{$this->attribute});		
	}
	
	/**
	 * Get attribute
	 * @return mixed
	 */
	public function get()
	{
		try {
			$data = @unserialize($this->owner->{$this->attribute});
			return is_array($data) ? $data : array();
		}
		catch(\Exception $e) {
			if($this->safeGet) return array();
			else throw $e;
		}
	}
	
	/**
	 * Set attribute
	 * @param array $value
	 */
	public function set($value)
	{	
		if(is_array($value)) {
			if(!$this->allowEmpty) {
				foreach($value as $idx=>$data) 
					if(empty($data) || !implode('', array_values($data))) unset($value[$idx]);
			}
		}
		elseif($this->safeGet) 
			$value = is_array(@unserialize($value)) ? unserialize($value) : array();
		
		$this->owner->{$this->attribute} = serialize($value);
		
		return true;
	}
	
	/**
	 * Get only actived
	 * @param boolean $preserveKeys сохранять ключи или нет.
	 * @return array
	 */
	public function getActive($preserveKeys=false)
	{
		$actived = array();
		 
		foreach($this->get() as $index=>$data) {
			if(A::get($data, 'active')) {
				if($preserveKeys) $actived[$index] = $data;
				else $actived[] = $data;
			}
		}
		
		return $actived;
	}
}