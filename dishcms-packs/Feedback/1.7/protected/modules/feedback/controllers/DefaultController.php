<?php
/**
 * Frontend default controller
 * 
 */
namespace feedback\controllers;

use \feedback\components\controllers\FrontController;

class DefaultController extends FrontController
{
	/**
	 * Index action.
	 * @param string $formId Идентификатор формы 
	 */
	public function actionIndex($formId)
	{
		$factory = new FeedbackFactory($formId);
		
		$this->render('index', $factory);
	}
}