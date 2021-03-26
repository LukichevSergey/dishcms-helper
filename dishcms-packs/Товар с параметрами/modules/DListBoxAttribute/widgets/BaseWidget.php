<?php
/**
 * Базовый класс для виджетов модуля DListBoxAttribute
 * 
 * @use \YiiHelper;
 * @use \AssetHelper;
 */
namespace DListBoxAttribute\widgets;

use \AttributeHelper as A;

use DListBoxAttribute\models\DListBoxAttribute;

class BaseWidget extends \CWidget
{
	/**
	 * Имя аттрибута со списком значений.
	 * @var string
	 */
	public $attribute;

	/**
	 * Заголовок
	 * @var string
	 */
	public $title;

	/**
	 * Css class for wrapper 
	 * @var string
	 */
	public $cssClass = '';
	
	/**
	 * Шаблон отображения
	 * @var string
	 */
	public $view = 'default';
	
	/**
	 * Заголовок по умолчанию
	 * @var string
	 */
	protected $defaultTitle = '';

	/**
	 * Имя класса модели с пространством имен
	 * @var string
	 */
	protected $modelClass;

	/**
	 * (non-PHPdoc)
	 * @see \CWidget::init()
	 */
	public function init()
	{
		//var_dump($this->attribute);
		DListBoxAttribute::register($this->attribute);

		$this->modelClass = DListBoxAttribute::getClassName($this->attribute);

		parent::init();
	}

	/**
	 * Get protected property self::$_modelClass
	 * @return string
	 */
	public function getModelClass()
	{
		return $this->modelClass;
	}

	/**
	 * Get widget title
	 * @param string $option Имя опции заголовка в опциях аттрибута.
	 * @return string
	 */
	public function getTitle($option='title')
	{
		return $this->title ?: A::get(\Yii::app()->getModule('DListBoxAttribute')->getAttributeOptions($this->attribute), $option, $this->defaultTitle);
	}

	/**
	 * Get ListBox attribute model
	 * @param string $scenario
	 * @param integer id модели аттрибута со списком значений.
	 * @return \DListBoxAttribute\models\DListBoxAttribute|NULL
	 */
	protected function getModel($scenario='', $id=null)
	{
		if($this->isExists()) {
			$modelClass = $this->modelClass;
				
			return $id ? $modelClass::model()->findByPk($id) : new $modelClass($scenario);
		}

		return null;
	}

	/**
	 * Проверка существования модели аттрибута со списком значений.
	 */
	protected function isExists()
	{
		return \Yii::app()->getModule('DListBoxAttribute')->attributeExists($this->attribute);
	}
}
