<?php
/**
 * Виджет выпадающего списка со значениями атрибута-списка
 */
namespace DListBoxAttribute\widgets;

use DListBoxAttribute\models\DListBoxAttribute;

class DropDownListWidget extends BaseModelWidget
{
	/**
	 * @see \CHtml::activeDropDownList() parameter $htmlOptions "prompt".
	 * @var string
	 */
	public $prompt = null;
	
	/**
	 * Сообщение при не выбранном значении.
// 	 * @var string
	 */
	public $promptAlert = 'Please choose one';
	
	/**
	 * @see \DListBoxAttribute\widgets\BaseWidget::$view
	 * @var string
	 */
	public $view = 'drop_down_list_default';
	
	public function run()
	{
		$items = $this->getItems();
		
		if(!$items) return true;
		
		$this->render($this->view, compact('items'));
	}
	
	/**
	 * Получить список значений для \CActiveForm::listBox()
	 * @return array see \CHtml::listData()
	 */
	protected function getItems()
	{
		$attributeOwner = $this->attributeOwner;
		
		return \CHtml::listData($this->model->$attributeOwner, 'id', 'title');
	}
	
	public function getOptions()
	{
		$attributeOwner = $this->attributeOwner;
		if(empty($this->model->$attributeOwner)) 
			return array();
		
		$options = array();
		foreach($this->model->$attributeOwner as $model) {
			$options[$model->id] = array('selected' => '');
		}
		
		return $options;
	}
}