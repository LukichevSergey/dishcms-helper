<?php
/**
 * Base backend controller
 *
 * Use \AdminController from dishman admin module, for integrate into "Dishman".
 * 
 */
namespace menu\components\controllers;

\Yii::import('application.modules.admin.components.AdminController');

class BackendController extends \AdminController
{
	/**
	 * Объект внешнего контроллера, который создал текущий.
	 * @var \CController
	 */
	public $ownerController;
	
	/**
	 * (non-PHPdoc)
	 * for Dishman integrate: 
	 * @see \AdminController::filters()
	 * other:
	 * @see \menu\components\controllers\BaseController::filters()
	 */
	/*public function filters()
	{
		return parent::filters();
	}*/
}