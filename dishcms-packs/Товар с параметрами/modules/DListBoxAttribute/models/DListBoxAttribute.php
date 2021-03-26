<?php
/**
 * Модель аттрибута со списком значений 
 * 
 * @property integer $id
 * @property string $title
 */
namespace DListBoxAttribute\models;

use \AttributeHelper as A;

class DListBoxAttribute extends \CActiveRecord
{	
	/**
	 * Простраство имен для модели аттрибута
	 * @var string
	 */
	const MODEL_NAMESPACE = '\DListBoxAttribute\runtime\models';
	
	/**
	 * Имя аттрибута
	 * @var string
	 */
	protected $attribute;
	
	/**
	 * Имя таблицы
	 * @var string
	 */
	private $_tableName;
	
	/**
	 * (non-PHPdoc)
	 * @see \CActiveRecord::__construct()
	 */
	public function __construct($scenario='insert')
	{
		$this->_tableName = $this->attribute; 
		parent::__construct($scenario);
	}
	
	/**
	 * Returns the static model of the specified AR class.
	 * @return Product the static model class
	 */
	public static function model($className=null)
	{
		if(is_null($className)) $className = get_called_class();
		return parent::model($className);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \CActiveRecord::tableName()
	 */
	public function tableName($withPrefix=true)
	{	
		return $withPrefix ? ('{{' . $this->_tableName . '}}') : $this->_tableName;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CActiveRecord::defaultScope()
	 */
	public function defaultScope()
	{
		return array(
			'order' => 'title'
		);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \CModel::rules()
	 */
	public function rules()
	{
		return array(
			array('title', 'required'),
			array('title', 'unique', 'caseSensitive'=>false),
			array('title', 'safe', 'on'=>'insert, update')
		);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \CModel::attributeLabels()
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'Id',
			'title' => 'Наименование'			
		);
	}	
	
	/**
	 * (non-PHPdoc)
	 * @see \CActiveRecord::init()
	 * 
	 * @param string|null $className Имя класса модели. 
	 *  Если передано значение NULL, берет имя класса из self::$_className 
	 */
	public function init()
	{
		$this->install();
	}
	
	/**
	 * Регистрация аттрибута
	 * @param string $attribute Имя регистрируемого аттрибута. 
	 */
	public static function register($attribute) 
	{
		if(self::validateAttribute($attribute)) {
			$className = ucfirst($attribute);
			
			$path = \Yii::getPathOfAlias('DListBoxAttribute') . DIRECTORY_SEPARATOR . '..' ;
			$path .= preg_replace('/\\\\+/', DIRECTORY_SEPARATOR, self::MODEL_NAMESPACE);
			if(!is_dir($path)) mkdir($path);
			 
			$filename = $path . DIRECTORY_SEPARATOR . $className . '.php';
			if(!file_exists($filename)) {
				$content = '<?php namespace ' . trim(self::MODEL_NAMESPACE, '\\') . ';';
				$content .= 'class ' . $className . ' extends \DListBoxAttribute\models\DListBoxAttribute {';
				$content .= 'protected $attribute = \'' . $attribute . '\';';
			 	$content .= '}'; 
				if(file_put_contents($filename, $content)) {
					// Инсталляция
					$modelClass = self::getClassName($attribute);
					return $modelClass::model()->install();
				}
			}		 
		}
		return false;
	}

	/**
	 * Инсталляция
	 * 
	 * @todo Обработка ошибок, сейчас возвращает всегда true.
	 */
	public function install()
	{
		$this->createTable();
		$this->integrateToDishCMS();
		return true;
	}
	
	/**
	 * Get property "attribute" value
	 * @return string
	 */
	public function getAttributeName() 
	{
		return $this->attribute;
	}
	
	/**
	 * Получить имя класса модели для аттрибута.
	 * @param string $attribute Имя аттрибута. 
	 * @return string
	 */
	public static function getClassName($attribute)
	{
		return self::MODEL_NAMESPACE . '\\' . ucfirst($attribute);
	}
	
	/**
	 * Валидация имени класса модели.
	 * @param string|null $className Имя класса модели. 
	 * @return boolean
	 */
	public static function validateAttribute($attribute)
	{
		return preg_match('/[a-z][a-z0-9_]+/i', $attribute);
	}
	
	/**
	 * Создание таблицы
	 */
	protected function createTable()
	{
		$query = 'CREATE TABLE IF NOT EXISTS `' . $this->tableName(false) . '` (
			`id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT \'Id\',
			`title` VARCHAR(255) NOT NULL DEFAULT \'\' COMMENT \'Title\'
		)';
		
		return (bool)$this->getDbConnection()->createCommand($query)->execute();
	}
	
	/**
	 * @section Интеграция в дишман 1
	 */
	/**
	 * Интеграция в DishCMS
	 */
	protected function integrateToDishCMS()
	{
		return $this->insertAdminMenuItem();
	}
	
	/**
	 * Вставка элемента меню в раздел администрирования
	 */
	protected function insertAdminMenuItem()
	{
		$url = 'dListBoxAttribute/' . $this->attribute;
		
		$params = array(':type' => 'url', ':options' => \CJSON::encode(array('url' => $url)));
		if(!\Menu::model()->exists('type=:type and options=:options', $params)) {
			$menu = new \Menu;
			$menu->title = A::get(\Yii::app()->getModule('DListBoxAttribute')->getAttributeOptions($this->attribute), 'listTitle', $this->attribute);
			$menu->type = 'url';
			$menu->options = \CJSON::encode(array('url' => $url));
			$menu->ordering = -1;
			$menu->hidden = 1;
			// Для интеграции с модулем многоуровнего меню
			if(\Menu::model()->getTableSchema()->getColumn('system')) {
				$menu->system = 1;
			}
			return $menu->save();
		}
		
		return true;
	}
}