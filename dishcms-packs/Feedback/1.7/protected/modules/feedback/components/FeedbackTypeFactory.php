<?php
/**
 * Feedback module.
 * 
 * Feedback type factory
 * 
 */
namespace feedback\components;

use \feedback\components\types as types;
use feedback\components\types\BaseType;

class FeedbackTypeFactory extends \CComponent
{
	/**
	 * Type name
	 * @var string
	 */
	protected $_name;
	
	/**
	 * Type model
	 * @var \feedback\components\types\BaseType
	 */
	protected $_model;
	
	/**
	 * Construct
	 * @param string $name feedback type name.
	 */
	public function __construct($name)
	{	
		if(!is_string($name) || !$name)
			throw new FeedbackTypeFactoryException("Invalid type name.");
		
		$this->_name = $name;
	}
	
	/**
	 * Get type class name.
	 * @param string $name feedback type name.
	 * @return string type class name.
	 */
	public function getTypeClassName($name=null)
	{
		if(!is_string($name) || !$name) $name = $this->_name;
		
		return '\feedback\components\types\\' . $name . 'Type';
	}
	
	/**
	 * Get model property value 
	 * @return \feedback\components\types\BaseType type model based on BaseType.
	 */
	public function getModel()
	{
		return $this->_model;		
	}
	
	/**
	 * Set feedback model
	 * @param \feedback\components\types\BaseType $model type model based on BaseType.
	 * @return boolean
	 */
	public function setModel($model)
	{
		if(!($model instanceof BaseType)) return false;
		
		$this->_model = $model;
		
		return true;
	}
	
	/**
	 * Factory
	 * @param string $name feedback type name.
	 * @param string $attribute attribute name.
	 * @param string $label attribute label.
	 * @return \feedback\components\types\BaseType feedback type object based on BaseType. 
	 */
	public static function factory($name, $attribute, $label='')
	{
		$factory = new self($name);
		
		$class = $factory->getTypeClassName();
		$factory->setModel(new $class($attribute, $label));
		 
		return $factory;
	}
}

/**
 * Feedback type factory exception class.
 * @see \Exception
 */

class FeedbackTypeFactoryException extends \Exception
{
}