<?php
/**
 * Контроллер
 *
 */
namespace extend\modules\banks\controllers;

use common\components\helpers\HArray as A;
use common\components\helpers\HYii as Y;
use settings\components\helpers\HSettings;

class DefaultController extends \Controller
{
	/**
	 * (non-PHPdoc)
	 * @see \CController::filters()
	 */
	public function filters()
	{
		return A::m(parent::filters(), [
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
	
	/**
	 * Action: Главная страница
	 */
	public function actionIndex()
	{
		$this->showCalculator=true;
		
		$settings=HSettings::getById('banks');
		
    	$this->seoTags([
			'meta_h1'=>$settings->meta_h1 ?: $this->getHomeTitle(),
			'meta_title'=>$settings->meta_title ?: $this->getHomeTitle(),
			'meta_key'=>$settings->meta_key,
			'meta_desc'=>$settings->meta_desc
		]);
    	
		$this->breadcrumbs->add($this->getHomeTitle());
		
		$this->render('index');
	}
	
	/**
	 * Action: Детальная страница
	 * @param integer $id model id 
	 */
	public function actionView($id)
	{
		throw new \CHttpException(404);
		
		$model=$this->loadModel('', $id);

		$this->render('view', compact('model'));
	}
	
	/**
	 * Action: Получить основной заголовок
	 * @return string
	 */
	public function getHomeTitle()
	{
		return \Yii::t('\extend\modules\banks\BanksModule.controllers/default', 'title');
	}
}
