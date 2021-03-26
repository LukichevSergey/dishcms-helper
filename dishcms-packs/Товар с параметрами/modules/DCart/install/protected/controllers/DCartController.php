<?php
/**
 * DCart module frontend controller.
 * @version 1.0
 */
class DCartController extends Controller
{
	/**
	 * (non-PHPdoc)
	 * @see \AdminController::filters()
	 */
 	public function filters()
	{
		return \CMap::mergeArray(parent::filters(), array(
			'ajaxOnly + add, clear, updateCount, getCount'
		));
	} 
	
	/**
	 * Добавление товара в корзину
	 * @param integer $id id товара
	 */
	public function actionAdd($id) 
	{
		$this->render('add', compact('id'));
	}
	
	/**
	 * Очистка корзины
	 */
	public function actionClear()
	{
		$this->render('clear');
	}
	
	/**
	 * Получить кол-во товара в корзине
	 */
	public function actionGetCount()
	{
		$this->render('getCount');
	}
	
	/**
	 * Обновить количество
	 */
	public function actionUpdateCount()
	{
		$this->render('updateCount');
	} 
	
	/**
	 * Удаление товара из корзины
	 */
	public function actionRemove()
	{
		$this->render('remove');
	}
}