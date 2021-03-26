<?php
/**
 * Link type
 * string
 */
namespace feedback\components\types;

class LinkType extends BaseType
{
	/**
	 * (non-PHPdoc)
	 * @see \feedback\components\types\BaseType::rules()
	 */
	public function rules()
	{
		return parent::rules();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \feedback\components\types\BaseType::getSqlColumnDefinition()
	 */
	public function getSqlColumnDefinition()
	{
		return '`' . $this->_name . '` VARCHAR(255) COMMENT "' . $this->_label . '"';
	}	

	/**
	 * (non-PHPdoc)
	 * @see \feedback\components\types\BaseType::format()
	 */
	public function format($value)
	{
		return \CHtml::link($value, $value);
	}

}