<?php
/**
 * Birthday type
 *
 */
namespace feedback\components\types;

class BirthdayType extends BaseType
{
	/**
	 * (non-PHPdoc)
	 * @see \feedback\components\types\BaseType::rules()
	 */
	public function rules()
	{
		return \CMap::mergeArray(parent::rules(), array(
			array($this->_name, 'date', 'format'=>'yyyy-MM-dd'),
		));
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \feedback\components\types\BaseType::getSqlColumnDefinition()
	 */
	public function getSqlColumnDefinition()
	{
		return '`' . $this->_name . '` DATE COMMENT "' . $this->_label . '"';
	}	
	
	/**
	 * (non-PHPdoc)
	 * @see \feedback\components\types\BaseType::normalize()
	 */
	public function normalize($value)
	{
		// uncomment for russian format date "dd.MM.yyyy"
		if(preg_match('/^(?P<day>\d{2})\.(?P<month>\d{2})\.(?P<year>\d{4})$/', $value, $date)) {
			$value = "{$date['year']}-{$date['month']}-{$date['day']}";
		}
		return $value;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \feedback\components\types\BaseType::format()
	 */
	public function format($value)
	{
		// uncomment for russian format date "dd.MM.yyyy"
		if(preg_match('/^(?P<year>\d{4})-(?P<month>\d{2})-(?P<day>\d{2})$/', $value, $date)) {
			$value = "{$date['day']}.{$date['month']}.{$date['year']}";
		}
		return $value;
	}
}