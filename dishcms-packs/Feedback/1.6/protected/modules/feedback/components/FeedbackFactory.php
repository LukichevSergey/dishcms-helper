<?php
/**
 * Feedback factory
 * 
 * @version 1.01
 */
namespace feedback\components;

use \AttributeHelper as A;
use \feedback\models\FeedbackModel;

class FeedbackFactory extends \CComponent
{
	/**
	 * Feedback ID
	 * @var string
	 */
	protected $_id;
	
	/**
	 * Feedback title.
	 * @var string
	 */
	protected $_title;
	
	/**
	 * Feedback configuration
	 * @var array
	 */
	protected $_config = array();
	
	/**
	 * Feedback model
	 * @var \feedback\components\FeedbackModelFactory
	 */
	protected $_model;
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
	}
	
	/**
	 * Get feedback id
	 * @return string
	 */
	public function getId()
	{
		return $this->_id;
	}
	
	/**
	 * Get configuration propery value.
	 * @return array
	 */
	public function getConfig()
	{
		return is_array($this->_config) ? $this->_config : array();
	}
	
	/**
	 * Get model property value 
	 * @return \feedback\components\FeedbackModelFactory
	 */
	public function getModelFactory()
	{
		return $this->_model;		
	}
	
	/**
	 * Set feedback model
	 * @param \feedback\components\FeedbackModelFactory $model
	 * @return boolean
	 */
	public function setModelFactory($model)
	{
		if(!($model instanceof FeedbackModelFactory)) return false;
		
		$this->_model = $model;
		
		return true;
	}
	
	/**
	 * Initialize factory
	 * 
	 * @param string $id Feedback ID. 
	 */
	public function init($id)
	{
		$this->_id = $id;
		
		// load configuration
		if(!($this->_config = include(\Yii::getPathOfAlias("feedback.configs.forms.{$id}") . '.php'))) {
			throw new FeedbackFactoryException('Configuration for "' . $id . '" feedback is failed.');
		}

		// set title
		$this->setTitle(A::get(reset($this->_config), 'title'));
		
		return true;
	}
	
	/**
	 * Get all feedback ids.
	 * @return array feedback ids.
	 */
	public static function getFeedbackIds()
	{
		$ids = array();
		
		$files = \DirHelper::getFiles(\Yii::getPathOfAlias('feedback.configs.forms'));
		foreach($files as $filename) {
			if(preg_match('/^(?P<id>[a-z][a-z0-9_]+)\.php$/', $filename, $matches))
				$ids[] = $matches['id'];		
		}
		
		return $ids;
	}
	
	/**
	 * Get title
	 * @return string
	 */
	public function getTitle()
	{
		return $this->_title;
	}
	
	/**
	 * Set title
	 * @param string $title
	 */
	public function setTitle($title)
	{
		$this->_title = $title;
	}
	
	/**
	 * Get option value by name
	 * @param string $name option name or deep path by "." (dot) delimiter.
	 * @param string $default default value.
	 * @return mixed option value.
	 */
	public function getOption($name, $default=null)
	{
		$config = $this->getConfig();
		// get only first configuration data
		$config = reset($config);
		if(strpos($name, '.') === false) {
			if(is_array($options = A::get($config, 'options'))) 
				return A::get($options, $name, $default);
		}
		else {
			$data = $config;
			$keys = explode('.', $name);
			$lastIdx = count($keys) - 1;
			foreach($keys as $idx=>$key) {
				if($idx < $lastIdx) {
					if(!is_array($data = A::get($data, $key)))
						return null;
				}
				else {
					return A::get($data, $key, $default);
				}
			}
		}
		
		return null;
	}
	
	/**
	 * Factory.
	 * @param string $id Feedback ID.
	 * @return FeedbackFactory
	 */
	public static function factory($id)
	{
		$factory = new self;
		// Инициализируем
		$factory->init($id);
		// Получаем модель
		$factory->setModelFactory(FeedbackModelFactory::factory($factory->getConfig()));
		
		
		return $factory;
	}
}

/**
 * Feedback factory exception class.
 * @see \Exception
 */

class FeedbackFactoryException extends \Exception 
{	
} 