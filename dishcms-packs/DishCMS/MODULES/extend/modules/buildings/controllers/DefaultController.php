<?php
/**
 * Контроллер
 *
 */
namespace extend\modules\buildings\controllers;

use common\components\helpers\HArray as A;
use common\components\helpers\HYii as Y;
use extend\modules\buildings\components\helpers\HBuildings;

class DefaultController extends \Controller
{
    public $layout = 'full';
    public $viewPathPrefix='extend.modules.buildings.views.default.';
    
	/**
	 * (non-PHPdoc)
	 * @see \CController::filters()
	 */
	public function filters()
	{
		return A::m(parent::filters(), [
		    'buildingsDisabled'
		]);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \CController::actions()
	 */
	public function actions()
	{
		return A::m(parent::actions(), [
		]);
	}
	
	public function filterBuildingsDisabled($filterChain)
	{
	    if(!\D::role('admin') && ((int)HBuildings::settings()->disabled > 0)) {
	        throw new \CHttpException(404);
	    }
	    $filterChain->run();
	}
	
	/**
	 * Action: Главная страница
	 */
	public function actionIndex()
	{
	    $this->setPageTitle($this->getHomeTitle());
	    $this->breadcrumbs->add($this->getHomeTitle());
	    
	    $this->render($this->viewPathPrefix.'index');
	}
	
	/**
	 * Action: Детальная страница
	 * @param integer $id model id 
	 */
	/*
	public function actionView($id)
	{
		$model=$this->loadModel('', $id);

		$this->render('view', compact('model'));
	}
	*/
	
	public function actionPorch($id)
	{
	    $model = $this->loadModel('\extend\modules\buildings\models\Porch', $id, true, ['scopes'=>'published']);
	    
	    $this->setPageTitle($model->getNumberTitle(true));
	    $this->breadcrumbs->add($this->getHomeTitle(), ['/buildings/index']);
	    $this->breadcrumbs->add($model->getNumberTitle(true));
	    
	    $this->render($this->viewPathPrefix.'porch', compact('model'));
	}
	
	public function actionFloor($id)
	{
	    $model = $this->loadModel('\extend\modules\buildings\models\Floor', $id, true, ['scopes'=>'published']);
	    if(!$model->porch->published) {
	        throw new \CHttpException(404);
	    }
	    
	    $title = $model->getNumberTitle(true) . ' ' . $model->porch->getNumberTitle(true);
	    $this->setPageTitle($title);
	    $this->breadcrumbs->add($this->getHomeTitle(), ['/buildings/index']);
	    $this->breadcrumbs->add($title);
	    //$this->breadcrumbs->add($model->porch->getNumberTitle(true), ['/buildings/porch', 'id'=>$model->porch->id]);
	    //$this->breadcrumbs->add($model->getNumberTitle(true));
	    
	    $this->render($this->viewPathPrefix.'floor', compact('model'));
	}
	
	public function actionApartment($id)
	{
	    $model = $this->loadModel('\extend\modules\buildings\models\Apartment', $id, true, ['scopes'=>['published', 'available']]);
	    if(!$model->floor->published || !$model->floor->porch->published) {
	        throw new \CHttpException(404);
	    }
	    
	    $title = $model->title;
	    $this->setPageTitle($title);
	    $this->breadcrumbs->add($this->getHomeTitle(), ['/buildings/index']);
	    // $this->breadcrumbs->add($model->floor->porch->getNumberTitle(true), ['/buildings/porch', 'id'=>$model->floor->porch->id]);
	    $this->breadcrumbs->add($model->floor->getNumberTitle(true) . ' ' . $model->floor->porch->getNumberTitle(true), ['/buildings/floor', 'id'=>$model->floor->id]);
	    $this->breadcrumbs->add($title);
	    
	    $this->render($this->viewPathPrefix.'apartment', compact('model'));
	}
	
	/**
	 * Action: Получить основной заголовок
	 * @return string
	 */
	public function getHomeTitle()
	{
		return \Yii::t('\extend\modules\buildings\BuildingsModule.controllers/default', 'title');
	}
}
