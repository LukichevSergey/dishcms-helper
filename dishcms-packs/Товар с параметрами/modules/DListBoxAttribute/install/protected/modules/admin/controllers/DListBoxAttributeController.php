<?php
/**
 * Backend controller for DListBoxAttribute module.
 *
 * @version 1.0
 */
class DListBoxAttributeController extends AdminController
{
	/**
	 * (non-PHPdoc)
	 * @see \AdminController::filters()
	 */
 	public function filters()
	{
		return \CMap::mergeArray(parent::filters(), array(
			'ajaxOnly + delete'
		));
	} 
	
	
	public function actionIndex($attribute) 
	{
		$this->render('index', compact('attribute'));
	}
	
	public function actionCreate($attribute)
	{
		$this->render('create', compact('attribute'));
	}
	
	public function actionUpdate($attribute, $id)
	{
		$this->render('update', compact('attribute', 'id'));
	}
	
	public function actionDelete($attribute, $id)
	{
		$this->render('delete', compact('attribute', 'id'));
	}
}