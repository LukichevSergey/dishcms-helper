<?php
/**
 * Base backend controller
 *
 * Use \AdminController from dishman admin module, for integrate into "Dishman".
 * 
 */
namespace feedback\components\controllers;

class BackendController extends \AdminController
{
	/**
	 * Объект внешнего контроллера, который создал текущий.
	 * @var \CController
	 */
	public $ownerController;
	
	/**
	 * (non-PHPdoc)
	 * @see \feedback\components\controllers\Controller::filters()
	 */
	/*public function filters()
	{
		return parent::filters();
	}*/
}