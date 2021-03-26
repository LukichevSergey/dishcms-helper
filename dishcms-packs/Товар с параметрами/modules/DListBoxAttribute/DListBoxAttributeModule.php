<?php
/**
 * Модуль атрибута со списком значений.
 */
Yii::setPathOfAlias('DListBoxAttribute', __DIR__);

use \AttributeHelper as A;
use DListBoxAttribute\models\DListBoxAttribute;

class DListBoxAttributeModule extends CWebModule
{
	/**
	 * Доступные аттрибуты.
	 * Могут быть переданы через запятую или массивом.
	 * @var string|array
	 */
	public $allowAttributes;
	
	/**
	 * Доступные аттрибуты.
	 * Хранит уже обработанные значения из установленного DListBoxAttributeModule::$allowAttributes
	 * @var array
	 */
	private $_allowAttributes = array();
	
	private $_attributeOptions = array();
	
	/**
	 * Получить правила маршрутизации
	 * @return array
	 */
	public function getUrlRules()
	{
		return array(
			'<module:(cp|admin)>/dListBoxAttribute/<attribute:[a-z][a-z_0-9]+>' => 'admin/dListBoxAttribute/index',
			'<module:(cp|admin)>/dListBoxAttribute/<attribute:[a-z][a-z_0-9]+>/<action:\w+>' => 'admin/dListBoxAttribute/<action>',
			'<module:(cp|admin)>/dListBoxAttribute/<attribute:[a-z][a-z_0-9]+>/<action:\w+>/<id:\d+>' => 'admin/dListBoxAttribute/<action>',
		);
	}
	
	public function init()
	{
		// this method is called when the module is being created
		// you may place code here to customize the module or the application
		
		// Инициализация аттрибутов.
		$this->_initAttributeModel();
		
		// import the module-level models and components
		$this->setImport(array(
			'DListBoxAttribute.models.*',
			'DListBoxAttribute.components.*',
			'DListBoxAttribute.components.behaviors.*',
		));
	}
	
	public function beforeControllerAction($controller, $action)
	{
		if(parent::beforeControllerAction($controller, $action))
		{
			// this method is called before any module controller action is performed
			// you may place customized code here
			return true;
		}
		else
			return false;
	}
	
	/**
	 * Проверка существования модели аттрибута со списком значения.
	 * 
	 * Разрешенные(доступные) аттрибуты задаются через конфигурацию модуля 
	 * в параметре DListBoxAttributeModule::$allowAttributes 
	 * 
	 * @param string $modelClass Имя модели аттрибута со списком значений.
	 * @return boolean
	 */
	public function attributeExists($attribute)
	{
		return in_array(strtolower($attribute), $this->_allowAttributes);
	}
	
	/**
	 * Get attribute options
	 * @param array $attribute
	 * @return array
	 */
	public function getAttributeOptions($attribute)
	{
		return A::get($this->_attributeOptions, strtolower($attribute), array()); 
	}
	
	/**
	 * Инициализация аттрибутов.
	 */
	private function _initAttributeModel() 
	{
		// Инициализация разрешенных атрибутов.
		$this->_allowAttributes = array();
		if(is_string($this->allowAttributes) && preg_match('/^[a-z_, ]+$/i', $this->allowAttributes)) {
			$this->allowAttributes = str_replace(' ', '', strtolower($this->allowAttributes));
			$this->allowAttributes = preg_replace('/,+/', ',', $this->allowAttributes);
			$this->_allowAttributes = explode(',', $this->allowAttributes);
		}
		elseif(is_array($this->allowAttributes))
		{
			foreach ($this->allowAttributes as $attribute=>$options) {
				if(!is_array($options)) $attribute = $options;
				if(!is_string($attribute) || !preg_match('/^[a-z_]+$/i', $attribute)) continue;
				
				if(is_array($options)) 
					$this->_attributeOptions[$attribute] = $options;
								
				$this->_allowAttributes[] = $attribute;
			}
		}
		
		// Регистрация моделей разрешенных атрибутов
		foreach ($this->_allowAttributes as $attribute) {
			DListBoxAttribute::register($attribute);
		}
	}
}
