<?php
/**
 * Базовый класс виджета аттрибута-списка со внешней моделью.
 */
namespace DListBoxAttribute\widgets;

use DListBoxAttribute\models\DListBoxAttributeRelation;

class BaseModelWidget extends BaseWidget
{
	/**
	 * DListBoxAttributeBehavior
	 * @var DListBoxAttributeBehavior
	 */
	public $behavior;
	
	/**
	 * Модель
	 * @see \CModel
	 * @var \CModel
	 */
	public $model;
	
	/**
	 * Имя аттрибута внешней модели
	 * @var string
	 */
	protected $attributeOwner;
	
	/**
	 * (non-PHPdoc)
	 * @see \DListBoxAttribute\widgets\BaseWidget::init()
	 */
	public function init()
	{
		parent::init();
		
		$this->attribute = $this->behavior->getAttributeListBoxName();
		$this->attributeOwner = $this->behavior->getAttributeName();
		
		$this->setOwnerAttribute();
	}
	
	/**
	 * Установить значения аттрибута внешней модели.
	 */
	protected function setOwnerAttribute()
	{
		// Если значение атрибута внешней модели пустое, получим значения.
 		$relationClassName = DListBoxAttributeRelation::getClassName($this->model, $this->attribute);
 		$attributeOwner = $this->attributeOwner;
 		if(!$this->model->$attributeOwner) {
 			$this->model->$attributeOwner = $relationClassName::model()->findAllByOwnerId($this->model->id, true);
 		}
	}
}