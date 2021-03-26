<?php
/**
 * Menu module
 * 
 * v.1.01
 */
class MenuModule extends CWebModule
{
	/**
	 * Url rules for CUrlManager
	 * @var array
	 */
	public $urlRules = array(
	);
	
	/**
	 * Controller map
	 * @var array
	 */
	public $controllerMap = array(
		'admin' => '\menu\controllers\AdminController',
		'aciSortableAdmin' => '\menu\controllers\AciSortableAdminController',
	);
	
	/**
	 * (non-PHPdoc)
	 * @see CModule::init()
	 */
	public function init()
	{
		// this method is called when the module is being created
		// you may place code here to customize the module or the application

		// import the module-level models and components
		$this->setImport(array(
			'menu.components.*',
			'menu.components.behaviors.*',
			'menu.components.controllers.*',
			'menu.components.helpers.*',
			'menu.configs.*',
			//'menu.models.*',
		));
		
		// publish assets
		// \AssetsHelper::publishAssets(\Yii::getPathOfAlias('menu.assets'), array(
		//	'jquery/jquery.ui.core.js', 
		//	'jquery/jquery.ui.widget.js', 
		//	'jquery/jquery.ui.mouse.js', 
		//	'jquery/jquery.ui.sortable.js', 
		//	'jquery/jquery.mjs.nestedSortable.js'
		// ));
	}

	/**
	 * (non-PHPdoc)
	 * @see CWebModule::beforeControllerAction()
	 */
	public function beforeControllerAction($controller, $action)
	{
		if(parent::beforeControllerAction($controller, $action))
		{
			// this method is called before any module controller action is performed
			// you may place customized code here
			return true;
		}
		else
			return false;
	}
}
