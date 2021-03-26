<?php
/**
 * DOrder module frontend controller.
 * 
 * @version 1.0
 */
class DOrderController extends Controller
{
	/**
	 * (non-PHPdoc)
	 * @see \AdminController::filters()
	 */
//  	public function filters()
// 	{
// 		return \CMap::mergeArray(parent::filters(), array(
// 			'ajaxOnly + add, clear, updateCount, getCount'
// 		));
// 	} 
	
	/**
	 * Главная страница модуля заказов
	 */
	public function actionIndex() 
	{
		$this->actionOrder();
	}
	
	/**
	 * Страница оформления заказа
	 */
	public function actionOrder()
	{
		$this->prepareSeo('Оформление заказа');
		
		$this->render('order');
	}
}