<?php
/**
 * 
 */
namespace feedback\controllers\actions;

class AdminAction extends \CAction
{
	public $feedbackId;
	
	public function run()
	{
		// call feedback module controller
		$controller = new \feedback\controllers\AdminController('\feedback\controllers\AdminController');
		$controller->ownerController = $this->getController();
		
		$content = $controller->actionIndex($this->feedbackId);
		
		$this->getController()->render('feedback', compact('content'));
	}
}