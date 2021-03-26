<?php
/**
 * Feedback module.
 * 
 * Feedback model factory
 * 
 */
namespace feedback\components;

use \AttributeHelper as A;
use \feedback\components\FeedbackTypeFactory;
use \feedback\models\FeedbackModel;

class FeedbackModelFactory extends \CComponent
{
	/**
	 * Table prefix for feedback table.
	 * @var string
	 */
	const TABLE_PREFIX = 'feedback_';
	
	/**
	 * Configuration for this model
	 * @var array
	 */
	protected $_config;
	
	/**
	 * Attributes.
	 * Array where each item like as 
	 * 	key is a name of attribute, 
	 * 	value is a FeedbackTypeFactory object  
	 * @var array
	 */
	protected $_attributes;
	
	/**
	 * Model 
	 * @var \feedback\models\FeedbackModel
	 */
	protected $_model;
	
	/**
	 * Constructor
	 * @param array $config Конфигурация формы.
	 */
	public function __construct($config)
	{
		$this->_config = $config;
	} 
	
	/**
	 * Initialize factory
	 * @param array $config Конфигурация формы. Если не передана, берется из $this->_config.
	 */
	public function init($config=null)
	{
		if(!$config) $config = $this->_config;
		
		if(!is_array($config)) 
			throw new FeedbackModelFactoryException("Feedback configuration is failed.");
			
		// @var string Feedback ID.
		$id = key($config);		
		if(!$id) 
			throw new FeedbackModelFactoryException("Feedback id not found.");
		
		$this->_model = new FeedbackModel;
		
		// set feedback table name
		$this->_model->setTableName(self::TABLE_PREFIX . $id);
		
		$definitions = array();
		foreach(A::getR($config[$id], 'attributes') as $name=>$options) {
			// Добавляем атрибут
			$this->_model->$name = A::get($options, 'default');
			$this->_attributes[$name] = FeedbackTypeFactory::factory(A::getR($options, 'type'), $name, A::get($options, 'label', $name));
			// Добавляем список элементов, если задан
			if(property_exists($this->_attributes[$name]->getModel(), 'items')) {
				$typeModel = $this->_attributes[$name]->getModel();
				$typeModel->items = A::get($options, 'items', array());
				$this->_attributes[$name]->setModel($typeModel);
			}  
			// Добавляем опредление поля для создания табилицы
			$definitions[] = $this->_attributes[$name]->getModel()->getSqlColumnDefinition(); 
			// Добавляем правила валидации 
			$rules = $this->_model->addRules($this->_attributes[$name]->getModel()->rules());
			if(isset($options['rules'])) $rules = $this->_model->addRules($options['rules']);
			// Добавляем метку атрибута
			$this->_model->addAttributeLabel($name, $this->_attributes[$name]->getModel()->getLabel());
		}
		// Создаем таблицу (метод создаст таблицу, если не создана)
		$this->createTable($definitions);
	}
	
	/**
	 * Get attributes.
	 * @return array
	 */
	public function getAttributes()
	{
		return $this->_attributes;
	}
	
	/**
	 * Get attribute by name
	 * @param string $name attribute name
	 * @return \feedback\components\FeedbackTypeFactory
	 */
	public function getAttribute($name)
	{
		return A::get($this->_attributes, $name);		
	}
	
	/**
	 * Get this "_model" property value.
	 * @return \feedback\models\FeedbackModel
	 */
	public function getModel()
	{
		if(!($this->_model instanceof FeedbackModel)) return null;
		
		$this->_model->refreshMetaData();
		return $this->_model;
	}
	
	/**
	 * Create table
	 * @param \CActiveRecord $model model.
	 * @param array $definitions extended definitions.
	 * @return integer number of rows affected by the execution. 
	 * @see \CDbCommand::execute() 
	 */
	protected function createTable($definitions) 
	{
		$sql = 'CREATE TABLE IF NOT EXISTS `' . $this->_model->tableName() . '` (
			`id` INT(11) NOT NULL AUTO_INCREMENT,
			`created` DATETIME COMMENT "Create time",
			`completed` TINYINT(1) NOT NULL DEFAULT 0 COMMENT "Is completed"' 
			. (empty($definitions) ? '' : (',' . implode(',', $definitions))) 
			. ', PRIMARY KEY (`id`)) ENGINE=INNODB CHARACTER SET utf8;';
		// @dump var_dump($sql);
		
		\Yii::app()->db->createCommand($sql)->execute();
		
		// @hook for integrate into Dishman >1.6.x
		// Добавляем пункт в меню админки, если уже не добавлен.
		$id = key($this->_config);
		$options = '{"model":"feedback","id":"'.$id.'"}';
		$sql = "SELECT `id` FROM menu WHERE `options`='{$options}'";
		$exists = \Yii::app()->db->createCommand($sql)->queryScalar();
		if(!$exists) {
			\Yii::app()->db->createCommand()->insert('menu', array(
				'title' => A::get($this->_config[$id], 'short_title', A::get($this->_config[$id], 'title', 'Feedback')),
				'type' => 'model',
				'options' => $options,
				'ordering' => -1,
				'default' => 0,
				'hidden' => 1
			));
		}
		
		return true;
	}
	
	/**
	 * Создание объекта модели формы
	 * @param array $config Конфигурация формы.
	 * @return \feedback\models\FeedbackModel Модель формы.
	 */
	public static function factory($config)
	{
		$factory = new self($config);
		// Инициализация
		$factory->init();
		
		return $factory;
	} 
}

/**
 * Feedback model component exception class.
 * @see \Exception
 */

class FeedbackModelFactoryException extends \Exception
{
}