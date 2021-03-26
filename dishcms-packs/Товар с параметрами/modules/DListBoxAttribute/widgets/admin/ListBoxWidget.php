<?php
/**
 * Раздел администрирования.
 * Виджет отображения аттрибута cо списком значений для внешней модели внедряемый в \CActiveForm
 *
 * !ВАЖНО!
 * В методе init() свойство $attribute заменяется на $attributeListBox, 
 * а переданное значение помещается в $attributeOwner.
 * Это сделано для более ясной передачи параметров из DListBoxAttributeBehavior.  
 *  
 * @author BorisDrevetsky
 * 
 */
namespace DListBoxAttribute\widgets\admin;

use DListBoxAttribute\widgets\admin\AdminModelWidget;
use DListBoxAttribute\models\DListBoxAttribute;
use DListBoxAttribute\models\DListBoxAttributeRelation;

class ListBoxWidget extends AdminModelWidget
{
	/**
	 * Форма.
	 * @see \CActiveForm
	 * @var \CActiveForm
	 */
	public $form;
	
	/**
	 * @see \DListBoxAttribute\widgets\BaseWidget::$view
	 * @var string
	 */
	public $view = 'listbox_with_checkbox';
	
	/**
	 * (non-PHPdoc)
	 * @see \CWidget::run()
	 */
	public function run()
	{
		if(!$this->getViewFile($this->view)) $this->view = 'listbox';
		$this->render($this->view, array('form'=>$this->form, 'model'=>$this->model));
	}
	
	/**
	 * Получить значение параметра $select для \CHtml::checkBoxList()
	 * @return array
	 */
	public function getSelect()
	{
		$className = DListBoxAttributeRelation::getClassName($this->model, $this->attribute);
		return \Yii::app()->db->createCommand()
			->select($className::model()->getAttributeListBoxId())
			->from($className::model()->tableName())
			->where(
				$className::model()->getAttributeOwnerId() . '=:ownerId',
				array(':ownerId' => $this->model->id)
			)
			->queryColumn();
	}
	 
	/**
	 * Получить список значений для \CActiveForm::listBox()
	 * @return array see \CHtml::listData()
	 */
	protected function getItems()
	{
		$className = DListBoxAttribute::getClassName($this->attribute);
		$items = $className::model()->findAll(array('select'=>'id, title', 'order'=>'title'));
		return \CHtml::listData($items, 'id', 'title');
	}
}