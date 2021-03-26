<?php
/**
 * Виджет действия удаления заказа раздела администрирования.
 *  
 * @use \AjaxHelper
 */
namespace DOrder\widgets\admin\actions;

use \DOrder\models\Order;

class DeleteWidget extends BaseAdminActionWidget
{
	/**
	 * (non-PHPdoc)
	 * @see CWidget::run()
	 */
	public function run()
	{
		$ajaxHelper = new \AjaxHelper();
		
		$item = \Yii::app()->request->getPost('item');
		$model = Order::model()->findByPk((int)$item);
		if($model && $model->delete()) {
			$ajaxHelper->success = true;
			$ajaxHelper->data = array(
				'count' => Order::model()->uncompleted()->count()
			);
		}
		
		$ajaxHelper->endFlush();
	}
}