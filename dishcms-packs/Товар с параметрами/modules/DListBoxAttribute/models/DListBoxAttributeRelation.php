<?php
/**
 * Модель связи модели аттрибута со списком значений с моделью(внешняя модель), в которую добавляется данный аттрибут.
 * 
 * @use \YiiHelper
 * 
 * Основные свойства
 * @property integer $id
 */
namespace DListBoxAttribute\models;

use \DListBoxAttribute\models\DListBoxAttribute as DListBoxAttributeModel;

class DListBoxAttributeRelation extends \CActiveRecord
{
	/**
	 * Простраство имен модели связи
	 * @var string
	 */
	const MODEL_NAMESPACE = '\DListBoxAttribute\runtime\models';
	
	/**
	 * Имя аттрибута для значения id модели аттрибута со списком значений.
 	 * @var string
	 */
	protected $attributeListBox;
	
	/**
	 * Имя аттрибута для значения id внешней модели
	 * @var string
	 */
	protected $attributeOwnerId;
	
	/**
	 * Имя аттрибута для значения id модели аттрибута со списком значений.
 	 * @var string
	 */
	protected $attributeListBoxId;
	
	/**
	 * Имя таблицы
	 * @var string
	 */
	protected $tableName;
	
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
		return $withPrefix ? "{{{$this->tableName}}}" : $this->tableName;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \CModel::rules()
	 */
	public function rules()
	{
		return array(
			array('title', 'requeried'),
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
	 * @see CActiveRecord::relations()
	 */
// 	public function relations()
// 	{
// 		return array(
// 			'dListBoxAttribute' => array(
// 				self::BELONGS_TO, 
// 				DListBoxAttributeModel::getClassName($this->attributeListBox), 
// 				$this->attributeListBoxId
// 			) 
// 		);	
// 	}
	
	/**
	 * (non-PHPdoc)
	 * @see \CActiveRecord::init()
	 * 
	 * @param object|string Имя класса или объект внешней модели.
	 * @param object|string Имя класса или объект модели аттрибута со списком значений.  
	 */
	public function init()
	{
		$this->createTable();
	}
	
	/**
	 * Регистрация модели связи
	 * 
	 * @param \СActiveRecord|string $owner Имя класса или объект внешней модели.
	 * @param string $attributeListBox Имя аттрибута со списком значений.
	 * @return boolean
	 */
	public static function register($owner, $attributeListBox)
	{
		$className = self::getClassName($owner, $attributeListBox, true);
		
		if(self::validateClassName($className)) {
				
			$path = \Yii::getPathOfAlias('DListBoxAttribute') . DIRECTORY_SEPARATOR . '..' ;
			$path .= preg_replace('/\\\\+/', DIRECTORY_SEPARATOR, self::MODEL_NAMESPACE);
			if(!is_dir($path)) mkdir($path);
	
			$filename = $path . DIRECTORY_SEPARATOR . $className . '.php';
			if(!file_exists($filename)) {
				$ownerName = strtolower(\YiiHelper::getClassName($owner));
				$content = '<?php namespace ' . trim(self::MODEL_NAMESPACE, '\\') . ';';
				$content .= 'class ' . $className . ' extends \DListBoxAttribute\models\DListBoxAttributeRelation {';
// 				$content .= 'public $' . $attributeListBox . '_id;';
// 				$content .= 'public $' . $ownerName . '_id;';
				$content .= 'protected $attributeListBox = \'' . $attributeListBox . '\';';
				$content .= 'protected $attributeOwnerId = \'' . $ownerName . '_id\';';
				$content .= 'protected $attributeListBoxId = \'' . $attributeListBox . '_id\';';
				$content .= 'protected $tableName = \'' . $ownerName . '_' . $attributeListBox . '\';';
				$content .= '}';
				file_put_contents($filename, $content);
			}
			return true;
		}
		return false;
	}
	
	/**
	 * Получить имя класса модели связи.
	 * @param \СActiveRecord|string $owner имя класса или объект внешней модели.
	 * @param string $attributeListBox имя аттрибута со списком значений.
	 * @param boolean $withoutNamespace возвратить без пространства имен
	 * @return string
	 */
	public static function getClassName($owner, $attributeListBox, $withoutNamespace=false)
	{
		$className = \YiiHelper::getClassName($owner, true) . ucfirst($attributeListBox); 
		return $withoutNamespace ? $className : (self::MODEL_NAMESPACE . '\\' . $className);
	}
	
	/**
	 * Get property "attributeListBox" value.
	 * @return string
	 */
	public function getAttributeListBox()
	{
		return $this->attributeListBox;		
	}
	
	/**
	 * Get property "attributeOwnerId" value
	 * @return string
	 */
	public function getAttributeOwnerId()
	{
		return $this->attributeOwnerId;
	}
	
	/**
	 * Get property "attributeListBoxId" value.
	 * @return string
	 */
	public function getAttributeListBoxId()
	{
		return $this->attributeListBoxId;
	}
	
	/**
	 * Валидация имени класса модели.
	 * @param string|null $className Имя класса модели.
	 * @return boolean
	 */
	public static function validateClassName($className)
	{
		return preg_match('/[a-z][a-z0-9_]+/i', $className);
	}
	
	/**
	 * Найти все значения для внешней модели.
	 * @param integer $ownerId id внешней модели.
	 * @param boolean $indexAsListBoxId возвращать в качестве ключей id атрибута-списка или нет. 
	 * @return multitype:CActiveRecord
	 */
	public function findAllByOwnerId($ownerId, $indexAsListBoxId=false)
	{
		return $this->findAll(array(
			'index' => $indexAsListBoxId ? $this->attributeListBoxId : 'id',
			'condition' => '`' . $this->attributeOwnerId . '`=' . (int)$ownerId
		));
	}
	
	/**
	 * Обновить у внешней модели привязанные значения атрибута-списка 
 	 * @param integer $ownerId Id внешей модели.
	 * @param array $attributeIds Массив с идентификаторами новых значений.
	 * @return integer the number of rows being updated.
	 */
	public function updateOwner($ownerId, $attributeIds=array())
	{
		// Валидация
		$ownerId = (int)$ownerId;
		if(is_array($attributeIds)) {
			foreach($attributeIds as $idx=>$id) {
				if(!(int)$id) unset($attributeIds[$idx]);
			}
		}
		else $attributeIds = null;
		
		if(!$ownerId) return true;
		
		// Удаляем из таблицы все удаляемые значения атрибута-списка.
		$c = new \CDbCriteria();
		$c->addCondition('`' . $this->attributeOwnerId . '`=' . (int)$ownerId);
		if($attributeIds) {
			$c->addCondition('`' . $this->attributeListBoxId . '` NOT IN(' . implode(',', $attributeIds) . ')');
		} 
		$this->deleteAll($c);
		
		// Если не переданы значения атрибутов, то УДАЛЯЮТСЯ ВСЕ привязанные значения,
		// поэтому можно просто вернуть true.
		if(!$attributeIds) return true;
		
		// Добавляем в таблицу новые записи
		$query = 'INSERT IGNORE INTO `' . $this->tableName . '` ';
		$query .= '(`' . $this->attributeOwnerId . '`, `' . $this->attributeListBoxId . '`)';
		$query .= ' VALUES ' . implode(',', array_map(function($id) use ($ownerId) { return "({$ownerId}, {$id})"; }, $attributeIds));
		
		$result = $this->dbConnection->createCommand($query)->execute();
		
		// Удаляем все битые записи
		// Можно закомментировать для увеличения быстродействия
		$attributeClassName = DListBoxAttributeModel::getClassName($this->attributeListBox);
		$query = 
			'DELETE FROM `' . $this->tableName . '` 
				WHERE `' . $this->attributeListBoxId . '` NOT IN (
					SELECT `id` FROM `'. $attributeClassName::model()->tableName(false) . '`)';    
		$this->dbConnection->createCommand($query)->execute();
		
		return $result;
	}
	
	/**
	 * Создание таблицы
	 */
	protected function createTable()
	{
		$query = 'CREATE TABLE IF NOT EXISTS `' . $this->tableName(false) . "` (
			`id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT 'Id',
			`{$this->attributeOwnerId}` INT(11) NOT NULL COMMENT 'Model id',
			`{$this->attributeListBoxId}` INT(11) NOT NULL COMMENT 'List box attribute model id',
			UNIQUE KEY `relation` (`{$this->attributeOwnerId}`, `{$this->attributeListBoxId}`),
			INDEX (`{$this->attributeOwnerId}`),
			INDEX (`{$this->attributeListBoxId}`)
		)";
		
		return $this->getDbConnection()->createCommand($query)->execute();
	}
}