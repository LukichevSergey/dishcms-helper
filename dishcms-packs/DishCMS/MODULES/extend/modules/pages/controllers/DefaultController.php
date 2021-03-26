<?php
/**
 * Контроллер
 *
 */
namespace pages\controllers;

use common\components\helpers\HArray as A;
use common\components\helpers\HYii as Y;

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
		$this->render('index');
	}
	
	/**
	 * Action: Детальная страница
	 * @param integer $id model id 
	 */
	public function actionView($id)
	{
		$model=$this->loadModel('', $id);

		$this->render('view', compact('model'));
	}
	
	/**
	 * Action: Получить основной заголовок
	 * @return string
	 */
	public function getHomeTitle()
	{
		return \Yii::t('PagesModule.controllers/default', 'title');
	}
}
