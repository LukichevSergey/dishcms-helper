<?php
/**
 * BannerSettings widget
 */
use settings\components\helpers\HSettings;

class BannerWidget extends \CWidget
{
	/**
	 * @var string шаблон отображения.
	 */
	public $view='main';
	
	/**
	 * @var array дополнительные параметры для шаблона отображения.
	 */
	public $params=[];
	
	/**
	 * (non-PHPdoc)
	 * @see CWidget::run()
	 */
	public function run()
	{
		$this->render($this->view, $this->params);	
	}
}