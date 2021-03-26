<?php
/**
 * Модуль корзины.
 * 
 */
class DCartModule extends CWebModule
{
	/**
	 * Получить правила маршрутизации
	 * @return array
	 */
	public function getUrlRules()
	{
		return array(
			'/dCart' => 'dCart/index',
			'/dCart/<action:\w+>' => 'dCart/<action>',
			'/dCart/<action:\w+>/<id:\d+>' => 'dCart/<action>',
		);
	}
	
	public function init()
	{
		// this method is called when the module is being created
		// you may place code here to customize the module or the application

		// import the module-level models and components
		$this->setImport(array(
			'DCart.models.*',
			'DCart.components.*',
		));
	}
		
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
