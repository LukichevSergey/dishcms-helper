<?php
/**
 * Виджет действия сохранения комментария раздела администрирования.
 *  
 * @use \AjaxHelper
 */
namespace DOrder\widgets\admin\actions;

use \DOrder\models\DOrder;

class CommentWidget extends BaseAdminActionWidget
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
			$model->comment = \CHtml::encode(\Yii::app()->request->getPost('comment', ''));
			if($model->save()) {
				$ajaxHelper->success = true;
				$ajaxHelper->data = array('comment' => $model->comment);
			}
		}
		
		$ajaxHelper->endFlush();
	}
}