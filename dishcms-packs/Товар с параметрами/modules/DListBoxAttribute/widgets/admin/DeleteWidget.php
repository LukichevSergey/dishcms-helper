<?php
/**
 * Раздел администрирования.
 * Виджет удаления модели аттрибута со списком значений.
 *
 * @author BorisDrevetsky
 * 
 * @use \AjaxHelper
 */
namespace DListBoxAttribute\widgets\admin;

use DListBoxAttribute\widgets\admin\AdminWidget;

class DeleteWidget extends AdminWidget
{
	/**
	 * Id модели аттрибута со списком значений.
	 * @var integer
	 */
	public $id;
	
	/**
	 * (non-PHPdoc)
	 * @see \CWidget::run()
	 */
	public function run()
	{
		$ajaxHelper = new \AjaxHelper();
		
		if($ajaxHelper->isAjaxRequest(1, true)) {
			if($model = $this->getModel('delete', $this->id)) {
				$model->delete();
				$ajaxHelper->data['id'] = $this->id;
				$ajaxHelper->success = true;
			}
		}
		
		$ajaxHelper->endFlush();
	}
}