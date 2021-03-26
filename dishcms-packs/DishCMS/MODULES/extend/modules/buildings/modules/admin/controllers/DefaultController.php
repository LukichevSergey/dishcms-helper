<?php
/**
 * Основной контроллер раздела администрирования модуля
 *
 */
namespace extend\modules\buildings\modules\admin\controllers;

use common\components\helpers\HYii as Y;
use extend\modules\buildings\modules\admin\components\BaseController;

class DefaultController extends BaseController
{
	/**
	 * (non-PHPDoc)
	 * @see BaseController::$viewPathPrefix;
	 */
	public $viewPathPrefix='extend.modules.buildings.modules.admin.views.default.';
	
	/**
	 * Action: Главная страница.
	 */
	public function actionIndex()
	{	
		$t=Y::ct('\extend\modules\buildings\modules\admin\AdminModule.controllers/default');
		
		$this->setPageTitle($t('page.title'));
		$this->breadcrumbs=[$t('page.title')];
		
		$this->render($this->viewPathPrefix.'index');
	}
}