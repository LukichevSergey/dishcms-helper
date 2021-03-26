<?php
/**
 * Виджет действия отображения списка заказов раздела администрирования. 
 * 
 * @use \AssetHelper
 */
namespace DOrder\widgets\admin\actions;

use \DOrder\models\DOrder;
use \DOrder\models\BaseForm;

class ListWidget extends BaseAdminActionWidget
{
	/**
	 * Кол-во заказов на страницу
	 * @var integer
	 */
	public $pageSize = 30;
	
	/**
	 * Payment type.
	 * @var int
	 */
	public $paymentType = \DOrder\models\DOrder::TYPE_CUSTOMER;
	
	/**
	 * (non-PHPdoc)
	 * @see CWidget::init()
	 */
	public function init()
	{
		\AssetHelper::publish(array(
			'path' => __DIR__ . DIRECTORY_SEPARATOR . 'assets',
			'js' => array('js/classes/DOrderListWidget.js', 'js/list_widget.js')
		));
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CWidget::run()
	 */
	public function run()
	{
		$c = new \CDbCriteria();
		$c->order = "id DESC";
		$count = DOrder::model()->count($c);
		
		$pages = new \CPagination($count);
		$pages->pageSize = $this->pageSize;
		$pages->applyLimit($c);
		
 		$model = DOrder::model()->findAll($c);
		
 		$viewPrefix = '';
 		switch($this->paymentType) {
 			case \DOrder\models\DOrder::TYPE_YANDEX:
 				$viewPrefix = 'ym_';
 				break;
 		}
 		
		if($model)
			$this->render($viewPrefix . 'list', compact('model', 'pages'));
		else
			$this->render($viewPrefix . 'list_empty');
	}
}



