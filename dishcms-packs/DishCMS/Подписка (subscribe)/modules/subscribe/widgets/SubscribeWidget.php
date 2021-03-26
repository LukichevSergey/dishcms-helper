<?php 
namespace subscribe\widgets;
use \subscribe\models\Subscribe;

class SubscribeWidget extends BaseWidget
{
	public $class_input = null;
	public $class_button = null;
	
	public $buttonTitle = null;

	public function run() {
	$this->class_input;

	\Yii::app()->clientScript->registerCssFile(
	    \Yii::app()->assetManager->publish(
	        \Yii::getPathOfAlias('subscribe.widgets.assets').'/front.css'
	    )
	);
		

		$classes = array('class_input'=>$this->class_input, 'class_button'=>$this->class_button);

		$subscribes = new Subscribe();
		$this->render('index', compact('subscribes', 'classes'));
	}
}