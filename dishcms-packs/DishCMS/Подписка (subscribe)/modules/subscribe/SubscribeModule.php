<?php

class SubscribeModule extends CWebModule
{


	public $controllerMap = array(
		'default' => array('class'=>'\subscribe\controllers\DefaultController'),
		'admin' => array('class'=>'\subscribe\controllers\AdminController'),
	);
# 'devcp/<controller>/<action>'=>'devadmin/<controller>/<action>',
	public function preinit() {
		\Yii::import('admin.components.AdminController');
		\Yii::app()->urlManager->addRules(
			array(

				'admin/subscribe/' => 'subscribe/admin/',
				'admin/subscribe/<action>/<id:\d+>' => 'subscribe/admin/<action>/<id:\d+>',
				'admin/subscribe/<action>' => 'subscribe/admin/<action>',
				'cp/subscribe/' => 'subscribe/admin/',
				'cp/subscribe/<action>' => 'subscribe/admin/<action>',
				'cp/subscribe/<action>/<id:\d+>' => 'subscribe/admin/<action>/<id:\d+>',
				), false);
	}

	public function init()
	{	
		
		$install_dir = __DIR__ . DS . 'data' . DS;
		if ( file_exists($install_dir . 'install.sql') ) {
		$sql = file_get_contents ( $install_dir . 'install.sql' );
		\Yii::app()->db->createCommand( $sql )->execute();
		rename( $install_dir . 'install.sql', $install_dir . 'installed.sql');
		}
		$this->setImport(array(
			'subscribe.models.*',
		));
	}

	public function getAssetsUrl()
	{
		$assetsUrl = \Yii::app()->assetManager->publish(\Yii::getPathOfAlias('subscribe.assets'));

		Yii::app()->clientScript->registerCssFile($assetsUrl.'/main_module.css');
		Yii::app()->clientScript->registerCssFile($assetsUrl.'/jquery.datetimepicker.css');
		Yii::app()->clientScript->registerScriptFile($assetsUrl.'/jquery.datetimepicker.js');
		return \Yii::app()->getModule('admin')->getAssetsUrl();
		
		#die;

/*		Yii::app()->clientScript->registerCssFile(
		    Yii::app()->assetManager->publish(
		        Yii::getPathOfAlias('subscribe.assets').'/main_module.css',
		        Yii::getPathOfAlias('subscribe.assets').'/jquery.datetimepicker.css'
		    )
		);*/
		    

/*		Yii::app()->clientScript->registerScriptFile(
		    Yii::app()->assetManager->publish(

		        Yii::getPathOfAlias('subscribe.assets').'/jquery.datetimepicker.js'
		    )
		);*/

		#Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('subscribe.assets') );

				
			#return $this->_assetsUrl;
		}

	public function run(){
		render('index');
	}

	public function registerImage($file){

		return \Yii::app()->assetManager->publish(\Yii::getPathOfAlias('subscribe.assets')).'/images/'.$file;
		 
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
