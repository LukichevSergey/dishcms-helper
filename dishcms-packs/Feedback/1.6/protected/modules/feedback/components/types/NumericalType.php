<?php
/**
 * Numerical type
 *
 */
namespace feedback\components\types;

class NumericalType extends BaseType
{
	/**
	 * (non-PHPdoc)
	 * @see \feedback\components\types\BaseType::rules()
	 */
	public function rules()
	{
		return \CMap::mergeArray(parent::rules(), array(
			array($this->_name, 'numerical', 'integerOnly'=>true, 'message'=>'Поле "{attribute}" должно быть целым числом'),
		));
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \feedback\components\types\BaseType::getSqlColumnDefinition()
	 */
	public function getSqlColumnDefinition()
	{
		return '`' . $this->_name . '` INT(11) COMMENT "' . $this->_label . '"';
	}	
}