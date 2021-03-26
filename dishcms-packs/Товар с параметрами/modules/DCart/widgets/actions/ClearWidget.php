<?php
/**
 * DCart "Clear cart" action widget
 * 
 * @use \AjaxHelper
 */
namespace DCart\widgets\actions;

class ClearWidget extends BaseActionWidget
{
	public $validateAttribute = 'clear';
	public $validateValue = 'clear';
	
	public function run()
	{
		$ajaxHelper = new \AjaxHelper();
		
		if(\Yii::app()->request->getPost($this->validateAttribute) == $this->validateValue) {
			if(\Yii::app()->cart->clear()) {
				$ajaxHelper->success = true;
				$this->prepareAjaxData($ajaxHelper);	
			}
		}
			
		$ajaxHelper->endFlush();
	}
}