<?php
/**
 * Основной контроллер раздела администрирования модуля
 *
 */
namespace ecommerce\modules\admin\controllers;

use common\components\helpers\HYii as Y;
use ecommerce\modules\admin\components\BaseController;

class DefaultController extends BaseController
{
	/**
	 * (non-PHPDoc)
	 * @see BaseController::$viewPathPrefix;
	 */
	public $viewPathPrefix='ecommerce.modules.admin.views.default.';
	
	/**
	 * Action: Главная страница.
	 */
	public function actionIndex()
	{	
		$t=Y::ct('\ecommerce\modules\admin\AdminModule.controllers/default');
		
		$this->setPageTitle($t('page.title'));
		$this->breadcrumbs=[$t('page.title')];
		
		$this->render($this->viewPathPrefix.'index');
	}
}