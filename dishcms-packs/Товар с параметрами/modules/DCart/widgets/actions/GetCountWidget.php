<?php
/**
 * DCart "Get count" action widget
 * 
 * @use \AjaxHelper
 */
namespace DCart\widgets\actions;

class GetCountWidget extends BaseActionWidget
{
	public function run()
	{
		$ajaxHelper = new \AjaxHelper();
		
		$hash = \Yii::app()->request->getPost('hash');
		$count = \Yii::app()->cart->getCount($hash);
		if($count) {
			$ajaxHelper->success = true;
			$ajaxHelper->data['count'] = $count;
		}
		
		$ajaxHelper->endFlush();
	}
}