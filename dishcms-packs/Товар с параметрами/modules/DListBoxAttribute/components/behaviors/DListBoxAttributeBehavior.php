<?php
/**
 * Поведение для модели товаров
 * 
 */
namespace DListBoxAttribute\components\behaviors;

use \AttributeHelper as A;
use \DListBoxAttribute\models\DListBoxAttribute;
use \DListBoxAttribute\models\DListBoxAttributeRelation;

class DListBoxAttributeBehavior extends \CBehavior
{
	/**
	 * Имя поведения в внешнем классе
	 * @var string
	 */
	public $name;
	
	/**
	 * Имя аттрибута идентификатора (primary key) внешней модели. 
	 * @var string
	 */
	public $attributeId = 'id';
	
	/**
	 * Имя аттрибута для хранения значений
 	 * @var string
	 */
	public $attribute;
	
	/**
	 * Имя аттрибута для одиночного значения
	 * @var string
	 */
	public $attributeOne;
	
	/**
	 * Имя аттрибута списка значений
	 * @var string
	 */
	public $attributeListBox;
	
	/**
	 * Заголовок для списка значений
	 * @var string
	 */
	public $title;
	
	/**
	 * Заголовок для одиночного значения
 	 * @var string
	 */
	public $titleOne;
	
	/**
	 * (non-PHPDoc)
	 * @see \CActiveRecord::relations()
	 */
	public function relations()
	{
		DListBoxAttribute::register($this->attributeListBox);
		DListBoxAttributeRelation::register($this->owner, $this->attributeListBox);
		
		$listBoxClass = DListBoxAttribute::getClassName($this->attributeListBox);
		$relationClass = DListBoxAttributeRelation::getClassName($this->owner, $this->attributeListBox); 
		return array(
			$this->attribute => array(
				\CActiveRecord::MANY_MANY, 
				$listBoxClass,
				$relationClass::model()->tableName(false) 
					. '(' . $relationClass::model()->getAttributeOwnerId() 
					. ',' . $relationClass::model()->getAttributeListBoxId() 
					. ')'
			)
		);
	}
	
	/**
	 * (non-PHPDoc)
	 * @see \CBehavior::init()
	 */
	public function init()
	{
		DListBoxAttribute::register($this->attributeListBox);
		DListBoxAttributeRelation::register($this->owner, $this->attributeListBox);
		
		$relationModelClass = DListBoxAttributeRelation::getClassName($this->owner, $this->attributeListBox);
		
		$relationModelClass::model()->init();
		
		$this->owner->onAfterSave = array($this, 'afterSave');
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
	 * Get property "attributeOne" value
	 * @return string
	 */
	public function getAttributeOneName()
	{
		return $this->attributeOne;	
	}
	
	/**
	 * Get property "attributeListBox" value
	 * @return string
	 */
	public function getAttributeListBoxName()
	{
		return $this->attributeListBox;
	}
	
	/**
	 * Сохранение аттрибутов
	 * При создании/редактировании виджетов:
	 * Сохранение происходит ТОЛЬКО из массива $_POST[$this->owner][$relationModelClass]
	 * Также для сохранения НЕОБХОДИМО, чтобы был задан $_POST[$this->owner][$relationModelClass . '-admin']
	 * @return boolean
	 */
	public function afterSave()
	{
		$relationModelClass = DListBoxAttributeRelation::getClassName($this->owner, $this->attributeListBox);
		
		if(!isset($_POST[get_class($this->owner)][$relationModelClass . '-admin'])) return true;
		
		$ids = isset($_POST[get_class($this->owner)][$relationModelClass]) ? $_POST[get_class($this->owner)][$relationModelClass] : array(); 
 		$attributeId = $this->attributeId;
		$relationModelClass::model()->updateOwner($this->owner->$attributeId, $ids);
		
		return true;
	}
}
