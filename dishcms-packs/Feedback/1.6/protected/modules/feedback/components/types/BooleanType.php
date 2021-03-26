<?php
/**
 * Boolean type
 *
 */
namespace feedback\components\types;

class BooleanType extends BaseType
{
	/**
	 * (non-PHPdoc)
	 * @see \feedback\components\types\BaseType::rules()
	 */
	public function rules()
	{
		return \CMap::mergeArray(parent::rules(), array(
			array($this->_name, 'boolean', 'trueValue'=>1, 'falseValue'=>0),
		));
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \feedback\components\types\BaseType::getSqlColumnDefinition()
	 */
	public function getSqlColumnDefinition()
	{
		return '`' . $this->_name . '` TINYINT(1) DEFAULT 0 COMMENT "' . $this->_label . '"';
	}	
}