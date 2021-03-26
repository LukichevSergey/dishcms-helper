<?php
/**
 * List type
 *
 */
namespace feedback\components\types;

use \AttributeHelper as A;

class ListType extends BaseType
{
	/**
	 * List items (value=>display)
	 * @var array
	 */
	public $items = array();
	
	/**
	 * (non-PHPdoc)
	 * @see \feedback\components\types\BaseType::rules()
	 */
	public function rules()
	{
		return \CMap::mergeArray(parent::rules(), array(
			array($this->_name, 'length', 'max'=>64),
			array($this->_name, 'in', 'range'=>array_keys($this->items)),
		));
	} 
	
	/**
	 * (non-PHPdoc)
	 * @see \feedback\components\types\BaseType::getSqlColumnDefinition()
	 */
	public function getSqlColumnDefinition()
	{
		return '`' . $this->_name . '` VARCHAR(64) COMMENT "' . $this->_label . '"';
	}

	/**
	 * (non-PHPdoc)
	 * @see \feedback\components\types\BaseType::format()
	 */
	public function format($value)
	{
		return A::get($this->items, $value, $value);
	}
}