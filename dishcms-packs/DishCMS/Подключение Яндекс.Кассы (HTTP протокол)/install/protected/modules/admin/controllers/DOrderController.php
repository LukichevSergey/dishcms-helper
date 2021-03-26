<?php
/**
 * DOrderController 
 * Backend controller for DOrder module.
 * 
 * @version 1.0 
 */
use common\components\helpers\HAjax;
use DOrder\models\DOrder;

class DOrderController extends AdminController
{
	/**
	 * (non-PHPdoc)
	 * @see AdminController::filters()
	 */
	public function filters()
	{
		return CMap::mergeArray(parent::filters(), array(
			array('DModuleFilter', 'name'=>'shop'),
			'ajaxOnly + completed, comment, delete, changePaid'
		));
	}

	public function actionChangePaid($id)
    {
        $ajax=HAjax::start();
        if($order=DOrder::model()->findByPk($id)) {
            $order->paid=($order->paid == 1) ? 0 : 1;
            $ajax->data=['paid'=>$order->paid];
            $ajax->success=$order->save();
        }
        $ajax->end();
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
