<?php
/**
 * Основной контроллер раздела администрирования модуля
 *
 */
namespace pages\modules\admin\controllers;

use common\components\helpers\HYii as Y;
use pages\modules\admin\components\BaseController;

class DefaultController extends BaseController
{
	/**
	 * (non-PHPDoc)
	 * @see BaseController::$viewPathPrefix;
	 */
	public $viewPathPrefix='pages.modules.admin.views.default.';
	
	/**
	 * Action: Главная страница.
	 */
	public function actionIndex()
	{	
		$t=Y::ct('\pages\modules\admin\AdminModule.controllers/default');
		
		$this->setPageTitle($t('page.title'));
		$this->breadcrumbs=[$t('page.title')];
		
		$this->render($this->viewPathPrefix.'index');
	}
}