<?php
/**
 * Phone type
 *
 */
namespace feedback\components\types;

class PhoneType extends BaseType
{
	/**
	 * (non-PHPdoc)
	 * @see \feedback\components\types\BaseType::rules()
	 */
	public function rules()
	{
		return \CMap::mergeArray(parent::rules(), array(
			array($this->_name, 'match', 'pattern'=>'/^\+7 \( \d{3} \) \d{3} - \d{2} - \d{2}$/'),
		));
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \feedback\components\types\BaseType::getSqlColumnDefinition()
	 */
	public function getSqlColumnDefinition()
	{
		return '`' . $this->_name . '` VARCHAR(128) COMMENT "' . $this->_label . '"';
	}	
}