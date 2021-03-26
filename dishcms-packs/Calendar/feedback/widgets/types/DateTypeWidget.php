<?php
/**
 * Date type widget
 *
 */
namespace feedback\widgets\types;

class DateTypeWidget extends BaseTypeWidget
{
	/**
	 * (non-PHPdoc)
	 * @see CWidget::init()
	 */
	public function init()
	{
	}
				
	/**
	 * (non-PHPdoc)
	 * @see \feedback\widgets\types\BaseTypeWidget::run()
	 */
	public function run($name, \feedback\components\FeedbackFactory $factory, \CActiveForm $form)
	{	
		$cs = \Yii::app()->getClientScript();
		
		$cs->registerScriptFile($cs->getCoreScriptUrl().'/jui/js/jquery-ui.min.js', \CClientScript::POS_END);
		$cs->registerScriptFile($cs->getCoreScriptUrl().'/jui/js/jquery-ui-i18n.min.js', \CClientScript::POS_END);
		$cs->registerCssFile($cs->getCoreScriptUrl().'/jui/css/base/jquery-ui.css');
		
		$this->render('date', compact('name', 'factory', 'form'));
	}
}