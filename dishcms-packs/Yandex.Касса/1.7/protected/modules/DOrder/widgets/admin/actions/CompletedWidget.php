<?php
/**
 * Виджет действия изменения статуса заказа "Обработан" раздела администрирования.
 *  
 * @use \AjaxHelper
 */
namespace DOrder\widgets\admin\actions;

use \DOrder\models\DOrder;

class CompletedWidget extends BaseAdminActionWidget
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
		if($model) {
			$model->completed = (int)!(bool)$model->completed;
			if($model->save()) {
				$ajaxHelper->success = true;
				$ajaxHelper->data = array(
					'status' => $model->completed, 
					'count' => DOrder::model()->uncompleted()->count()
				);
			}
		}
		
		$ajaxHelper->endFlush();
	}
}
