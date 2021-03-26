<?php
/**
 * Виджет действия удаления заказа раздела администрирования.
 *  
 * @use \AjaxHelper
 */
namespace DOrder\widgets\admin\actions;

use \DOrder\models\DOrder;

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
		$model = DOrder::model()->findByPk((int)$item);
		if($model && $model->delete()) {
			$ajaxHelper->success = true;
			$ajaxHelper->data = array(
				'count' => DOrder::model()->uncompleted()->count()
			);
		}
		
		$ajaxHelper->endFlush();
	}
}