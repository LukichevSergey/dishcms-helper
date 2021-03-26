<?php
namespace ext\D\dynamicAttribute\widgets;

use \AttributeHelper as A;

class DynamicAttributeWidget extends \CWidget
{
	/**
	 * @var \ext\D\dynamicAttribute\behaviors\DynamicAttributeBehavior behavior.
	 */
	public $behavior;
	
	/**
	 * @var string door size data attribute name.
	 */
	public $attribute;
	
	/**
	 * Массив заголовков данных
	 * "active" - is RESERVED KEY!
	 * array(key=>title)
	 * @var array
	 */
	public $header = array();
	
	/**
	 * Список ключей, которые будут только для чтения.
	 * Игнорируется ключ "active".
	 * array(key)
	 * @var array
	 */
	public $readOnly = array();
	
	/**
	 * Данные по умолчанию
	 * Индексы элементов должны совпадать индексами массива загловков.
	 * array(array(key=>value)) 
	 * @var array
	 */
	public $default = array();
	
	/**
	 * Не отображать кнопку добавления
	 * @var boolean
	 */
	public $hideAddButton = false;
	
	public function init()
	{
		\AssetHelper::publish(array(
			'path' => __DIR__ . DS . 'assets',
			'js' => array('js/DynamicAttributesWidget.js', 'js/dynamic_attributes_widget.js'),
			'css' => 'css/default.css'
		));
		
		\Yii::app()->clientScript->registerScript(
			'dynamicAttributesWidget' . $this->attribute, 
			'DynamicAttributesWidget.init("'.$this->attribute.'");', 
			\CClientScript::POS_END
		);
	}
	
	public function run()
	{
		$this->render('default');
	}
	
	/**
	 * Get row data by index.
	 * @param integer $index row index
	 * @return array
	 */
	public function getRowData($index) 
	{
		return A::get($this->behavior->owner->{$this->$attribute}, $index, A::get($this->default, $index, null));		
	}
	
	/**
	 * Generate row HTML code. 
	 * @param integer $index row index. Если передано значение NULl, 
	 * генерится код шаблона для новых элементов.
	 * @param array $data row data. 
	 * @return string html code
	 */
	public function generateRow($index, $data=array()) {
		$name = \CHtml::activeName($this->behavior->owner, $this->attribute);
	
		$isTemplate = is_null($index);
		if(is_null($index)) $index = '{{daw-index}}';
		
		$html = '<tr><td align="center">';
		$html .= \CHtml::checkBox($name . "[{$index}][active]", A::get($data, 'active', false), array(
				'class'=>'daw-inpt-active',
				'title'=>'Отображать на сайте',
				'value'=>1,
				'disabled'=>$isTemplate,
		));
		$html .= '</td>';
	
		foreach($this->header as $key=>$title) {
			$value = is_null($index) ? '' : A::q(A::get($data, $key, ''));
			$html .= '<td>';
			
			if(in_array($key, $this->readOnly)) {
				$html .= \CHtml::hiddenField($name . "[{$index}][{$key}]", $value, array('class'=>'daw-inpt', 'readonly'=>true, 'disabled'=>$isTemplate));
				$html .= $value;
			}
			else {
				$html .= \CHtml::textField($name . "[{$index}][{$key}]", $value, array('class'=>'daw-inpt', 'maxlength'=>255,'disabled'=>$isTemplate));
			}
			$html .= '</td>';
		}
	
		$html .= '<td align="center"><button class="default-button daw-btn-remove">Удалить</button></td>';
		$html .= '</tr>';
	
		return $html;
	}	
}
