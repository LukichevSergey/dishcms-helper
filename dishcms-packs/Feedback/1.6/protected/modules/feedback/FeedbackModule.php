<?php
/**
 * Feedback module
 * 
 * v.1.0
 */
class FeedbackModule extends CWebModule
{
	/**
	 * Url rules for CUrlManager
	 * @var array
	 */
	public $urlRules = array(
		'cp/feedback/<feedbackId:[a-z][a-z_0-9]+>' => 'admin/feedback/index',
		'cp/feedback/<feedbackId:[a-z][a-z_0-9]+>/<action:\w+>/<id:\d+>' => 'admin/feedback/index',
		'cp/feedback/<feedbackId:[a-z][a-z_0-9]+>/<action:\w+>' => 'admin/feedback/index',
		'admin/feedback/<feedbackId:[a-z][a-z_0-9]+>' => 'admin/feedback/index',
		'admin/feedback/<feedbackId:[a-z][a-z_0-9]+>/<action:\w+>/<id:\d+>' => 'admin/feedback/index',
		'admin/feedback/<feedbackId:[a-z][a-z_0-9]+>/<action:\w+>' => 'admin/feedback/index',
		'feedback/<feedbackId:[a-z][a-z_0-9]+>/<controller>/<action:\w+>/<id:\d+>' => 'feedback/<controller>/<action>',
		'feedback/<feedbackId:[a-z][a-z_0-9]+>/<controller>/<action:\w+>' => 'feedback/<controller>/<action>',
	);
	
	/**
	 * Controller map
	 * @var array
	 */
	public $controllerMap = array(
		'ajax' => '\feedback\controllers\AjaxController',
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
			'feedback.components.*',
			'feedback.components.controllers.*',
			'feedback.components.types.*',
			'feedback.configs.*',
			// 'feedback.configs.forms.*',
			'feedback.models.*',
		));
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
	
	public function getVersion()
	{
		return '1.01';
	}
}
