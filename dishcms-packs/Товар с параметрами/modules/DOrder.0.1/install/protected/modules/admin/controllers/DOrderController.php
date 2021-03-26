<?php
/**
 * DOrderController 
 * Backend controller for DOrder module.
 * 
 * @version 1.0 
 */
class DOrderController extends AdminController
{
	/**
	 * (non-PHPdoc)
	 * @see \AdminController::filters()
	 */
 	public function filters()
	{
		return \CMap::mergeArray(parent::filters(), array(
			'ajaxOnly + completed, comment, delete'
		));
	} 
	
	/**
	 * Index action
	 */
	public function actionIndex() 
	{
		$this->actionList();
	}
	
	/**
	 * Страница списка заказов
	 */
	public function actionList()
	{
		$this->pageTitle = 'Заказы - '.$this->appName;
		
		$this->render('list');
	}
	
	/**
	 * Изменение статуса заказа "Обработан"
	 */
	public function actionCompleted()
	{
		$this->render('completed');
	}
	
	/**
	 * Сохранение комментария
	 */
	public function actionComment()
	{
		$this->render('comment');
	}
	
	/**
	 * Удаление заказа
	 */
	public function actionDelete()
	{
		$this->render('delete');
	}
}