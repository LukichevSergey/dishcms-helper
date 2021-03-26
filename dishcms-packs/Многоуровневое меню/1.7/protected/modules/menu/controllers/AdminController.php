<?php
/**
 * Admin controller for menu module 
 */
namespace menu\controllers;

use \menu\components\controllers\BackendController;
use \menu\models\Menu;

class AdminController extends BackendController
{
	/**
	 * Menu Sorting page
	 */
	public function actionSort()
	{
		$menu = Menu::model()->ordering()->findAll();
		
		$this->render('sort', compact('menu'));
	}
	
}