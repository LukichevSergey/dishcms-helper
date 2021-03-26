<?php
/**
 * Admin backend controller
 *
 */
namespace feedback\controllers;

use \AttributeHelper as A;
use \feedback\components\controllers\BackendController;
use \feedback\components\FeedbackFactory;

class AdminController extends BackendController
{
	/**
	 * (non-PHPdoc)
	 * @see \CController::actions()
	 */
	public function actions()
	{
		$actions = array();
	
		$feedbacks = FeedbackFactory::getFeedbackIds();
		foreach($feedbacks as $feedbackId) {
			$actions[$feedbackId] = array(
				'class' => '\feedback\controllers\actions\AdminAction',
				'feedbackId' => $feedbackId
			);
		}
	
		return $actions;
	}
	
	/**
	 * Index action
	 * @param string $feedbackId feedback id.
	 * @return string page content.
	 */
	public function actionIndex($feedbackId)
	{
		// Вызов дополнительных действий 
		if($action = \Yii::app()->request->getParam('action')) {
			switch($action) {
				case 'changeCompleted':
					$this->actionChangeCompleted($feedbackId);
					\Yii::app()->end();
					break;
				case 'delete':
					$this->actionDelete($feedbackId);
					\Yii::app()->end();
					break;
			}
		}
		
		$factory = FeedbackFactory::factory($feedbackId);
		
		// Set page title.
		$title = $factory->getTitle();
		$this->pageTitle = "{$title} - {$this->appName}";
		
		$model = $factory->getModelFactory()->getModel();
		
		$dataProvider = $model->getDataProvider(new \CDbCriteria(array('order'=>'`created` DESC')));
		$uncompletedCount = $model->uncompleted()->count();
		
		// Если контроллер создан приложением, рендрим отображение, 
		// иначе возвращаем содержимое отображения.

		// publish assets
		\AssetHelper::publish(array(
			'path' 	=> \Yii::getPathOfAlias('feedback.assets'),
			'js' 	=> array('js/FeedbackAdmin.js')
		));
		
		$compact = compact('factory', 'dataProvider', 'uncompletedCount', 'title');
		if(!($this->ownerController instanceof \CController)) {
			$this->ownerController = $this;
			$this->render('feedback.views.admin.index', $compact);
		}
		else
			return $this->render('feedback.views.admin.index', $compact, true); 
	}
	
	/**
	 * Change feedback completed status action.
	 * @param string $feedbackId feedback id.
	 */
	public function actionChangeCompleted($feedbackId)
	{		
		$id = \Yii::app()->request->getPost('id');
		$result = array('success'=>false);
		
		$factory = FeedbackFactory::factory($feedbackId);

		$model = $factory->getModelFactory()->getModel(); 
		if($record = $model->findByPk((int)$id)) {
			$completed = !(bool)$record->completed;
			if($model->updateCompletedByPk($id, $completed)) {
				$result = array(
					'success'=>true,
					'status' => $completed,
					'count' => $model->uncompleted()->count()
				);
			}
		} else {
			$result['message'] = 'request not valid';
		}
	
		echo \CJSON::encode($result);
	
		\Yii::app()->end();
	}
	
	/**
	 * (Ajax) Delete a model
	 *
	 * @param string $feedbackId feedback id.
	 */
	protected function actionDelete($feedbackId)
	{
		$id = \Yii::app()->request->getPost('id');
		
		$factory = FeedbackFactory::factory($feedbackId);
		$model = $factory->getModelFactory()->getModel();
		
		$model->deleteByPk($id);
	
		echo \CJSON::encode(array('id'=>$id, 'uncompletedCount'=>$model->uncompleted()->count()));
		 
		\Yii::app()->end();
	}
}