<?php
/**
 * DCart "Remove" action widget
 * 
 * @use \AjaxHelper
 */
namespace DCart\widgets\actions;

class RemoveWidget extends BaseActionWidget
{
	public function run()
	{
		$ajaxHelper = new \AjaxHelper();
		
		$hash = \Yii::app()->request->getPost('hash');

		$ajaxHelper->success = \Yii::app()->cart->remove($hash);
		
		if($ajaxHelper->success) {
			$this->prepareAjaxData($ajaxHelper);
		}
			
		$ajaxHelper->endFlush();
	}
}