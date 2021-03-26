<?php
/**
 * DOrder module
 * 
 * Модуль работы с заказами
 *
 */
class DOrderModule extends CWebModule
{
	/**
	 * Имя таблицы заказов
	 */
	public $tableName = 'order';
	
	/**
	 * Алиас frontend контроллера.  
	 * @var string
	 */
	public $frontendControllerAlias = 'order';
	
	/**
	 * Получить правила маршрутизации
	 * @return array
	 */
	public function getUrlRules()
	{
		return array(
			'/' . $this->frontendControllerAlias => 'dOrder/index',
			'/' . $this->frontendControllerAlias . '/<action:\w+>' => 'dOrder/<action>',
			'/' . $this->frontendControllerAlias . '/<action:\w+>/<id:\d+>' => 'dOrder/<action>',
			'<module:(cp|admin)>/dOrder' => 'admin/dOrder/index',
			'<module:(cp|admin)>/dOrder/<action:\w+>' => 'admin/dOrder/<action>',
			'<module:(cp|admin)>/dOrder/<action:\w+>/<id:\d+>' => 'admin/dOrder/<action>',
		);
	}
	
	public function init()
	{
		// this method is called when the module is being created
		// you may place code here to customize the module or the application

		// import the module-level models and components
		$this->setImport(array(
			'DOrder.models.*',
			'DOrder.components.*',
		));
		
		// install models
		\DOrder\models\Order::install($this->tableName);
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
