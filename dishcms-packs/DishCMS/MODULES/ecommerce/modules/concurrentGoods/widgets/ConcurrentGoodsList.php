<?php
/**
 * Виджет выбора сопутсвующего товара.
 */
namespace ecommerce\modules\concurrentGoods\widgets;

use common\components\helpers\HDb;
use ecommerce\modules\concurrentGoods\models\ConcurrentGoods;
use crud\components\helpers\HCrud;

class ConcurrentGoodsList extends \common\components\base\Widget
{
	/**
	 * @var string имя поля.
	 */
	public $name='concurrent_goods';
	
	/**
	 * @var array|NULL идентификаторы сопутствующих товаров.
	 * По умолчанию (NULL) - все. 
	 */
	public $ids=null;
	
	/**
	 * @var string идентификатор настроек GRUD. 
	 * По умолчанию "ecommerce_concurrent_goods".
	 */
	public $cid='ecommerce_concurrent_goods';
	
	/**
	 * @var string категория сортировки поведения 
	 * \common\ext\sort\behaviors\SortBehavior
	 * По умолчанию "ecommerce_concurrent_goods".
	 */
	public $sort='ecommerce_concurrent_goods';
	
	/**
	 * @var string|array имя атрибута заголовка, либо массив вида
	 * array(textField=>callable), где callable функция преобразования 
	 * заголовка вида function(&model, $attribute) {}
	 * Может быть передана только функция, в этом случае имя атрибута
	 * будет использовано заданное по умолчанию.
	 * По умолчанию "title".
	 */
	public $textField='title';
	
	/**
	 * @var string|array имя атрибута значения, либо массив вида
	 * array(valueField=>callable), где callable функция преобразования 
	 * заголовка вида function(&model, $attribute) {}
	 * Может быть передана только функция, в этом случае имя атрибута
	 * будет использовано заданное по умолчанию.
	 * По умолчанию "id".
	 */
	public $valueField='id';
	
	/**
	 * @var string|array имя атрибута группировки, либо массив вида
	 * array(groupField=>callable), где callable функция преобразования 
	 * заголовка вида function(&model, $attribute) {}
	 * По умолчанию (пустая строка) не задано.
	 */
	public $groupField='';
	
	/**
	 * @var array|NULL данные пустого элемента.
	 * По умолчанию (NULL) не задан.
	 */
	public $empty=null;
	
	/**
	 * @var string|NULL дополнительные поля выборки.
	 * По умолчанию (NULL) не заданы.
	 */
	public $select=null;
	
	/**
	 * @var \CDbCriteria|array|NULL дополнительный критерий для выборки.
	 * По умолчанию (NULL) не задан.
	 */
	public $criteria=null;
	
	/**
	 * (non-PHPDoc)
	 * @see \CActiveForm::checkBoxList() параметр $data.
	 */
	public $data=null;
	
	/**
	 * (non-PHPDoc)
	 * @see \common\components\widgets\form\BaseField::$htmlOptions
	 */
	public $htmlOptions=[
		'container'=>'div',
		'labelOptions'=>['style'=>'display:inline-block !important;font-weight:normal;']
	];
	
	/**
	 * (non-PHPDoc)
	 * @see \common\components\widgets\form\BaseField::$view
	 */
	public $view='concurrent_goods';
	
	/**
	 * (non-PHPdoc)
	 * @see \common\components\widgets\form\BaseField::init()
	 */
	public function init()
	{
		parent::init();
		
		if(is_callable($this->textField)) $this->textField=['title'=>$this->textField];
		if(is_callable($this->valueField)) $this->valueField=['id'=>$this->valueField];
	}
	
	/**
	 * Получить данные списка.
	 */
	public function getData()
	{
		if($this->data === null) {
			$criteria=HDb::criteria($this->criteria);
			if($this->select) {
				$criteria=HDb::addScopes(['select'=>[$this->select, false]], $criteria);
			}
			
			if(is_array($this->ids) && count($this->ids)) {
				$criteria->addInCondition('`t`.`id`', $this->ids);
			}
			
			$this->data=HCrud::getById($this->cid)
				->scopeSort($this->sort)
				->listData($this->textField, $criteria, $this->empty, $this->valueField, $this->groupField);
			
			if(!$this->data) $this->data=[];
		}
		
		return $this->data;
	}
}