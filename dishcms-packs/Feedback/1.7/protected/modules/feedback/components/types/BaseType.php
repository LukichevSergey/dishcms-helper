<?php
/**
 * Abstract class of base type for feedback fields types.
 *
 */
namespace feedback\components\types;

use feedback\widget\FeedbackWidget;

abstract class BaseType implements IBaseType
{
	/**
	 * Attribute name
	 * @var string
	 */
	protected $_name;
	
	/**
	 * Attribute label
	 * @var string
	 */
	protected $_label;
	
	/**
	 * Constructor
	 * @param string $name attribute name.
	 * @param string $label attribute label.
	 */
	public function __construct($name, $label='')
	{
		$this->_name = $name;
		$this->_label = $label;
	}
	
	/**
	 * Get attribute name
	 * @return string
	 */
	public function getName()
	{
		return $this->_name;
	}
	
	/**
	 * Get attribute label.
	 * @return string
	 */
	public function getLabel()
	{
		return $this->_label;	
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \feedback\components\types\IBaseType::rules()
	 */
	public function rules() 
	{
		return array(
			array($this->_name, 'safe')
		);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \feedback\components\types\IBaseType::getSqlColumnDefinition()
	 */
	abstract public function getSqlColumnDefinition();
	
	/**
	 * (non-PHPdoc)
	 * @see \feedback\components\types\IBaseType::normalize()
	 */
	public function normalize($value)
	{
		return $value;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \feedback\components\types\IBaseType::format()
	 */
	public function format($value)
	{
		return $value;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \feedback\components\types\IBaseType::getWidget()
	 */
	public function getWidget()
	{
		$widgetClassName = '\feedback\widgets\types\\' . preg_replace('/.*?([^\\\\]+)$/', '\\1', get_called_class()) . 'Widget';
		
		return new $widgetClassName();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \feedback\components\types\IBaseType::widget()
	 */
	public function widget(\feedback\components\FeedbackFactory $factory, \CActiveForm $form, $params=array())
	{
		$widget = $this->getWidget();
		$widget->params = is_array($params) ? $params : array();
		return $widget->run($this->_name, $factory, $form);
	}
} 