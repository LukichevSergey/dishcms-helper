<?php
/**
 * Aci sortable admin controller for menu module
 */
namespace menu\controllers;

use \AjaxHelper;
use \menu\components\controllers\BackendController;
use \menu\models\Menu;

class AciSortableAdminController extends BackendController
{
	/**
	 * (non-PHPdoc)
	 * @see \AdminController::filters()
	 */
	public function filters()
	{
		return \CMap::mergeArray(parent::filters(), array(
			'ajaxOnly + save'
		));
	}
	
	/**
	 * Save aci menu ordering ajax action
	 * @throws CHttpException
	 */
	public function actionSave()
	{
		$ajax = new \AjaxHelper();
		$ajax->success = Menu::model()->updateTree(\Yii::app()->request->getPost('items'));
		$ajax->errorDefaultMessage = 'Произошла ошибка на сервере, порядок не был сохранен';
		$ajax->endFlush();
	}
}