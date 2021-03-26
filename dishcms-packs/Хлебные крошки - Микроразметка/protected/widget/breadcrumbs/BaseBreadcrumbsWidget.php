<?php
/**
 * Базовый класс для виджетов хлебных крошек сайта.
 */
Yii::import('widget.breadcrumbs.BreadcrumbsArrayWidget');

class BaseBreadcrumbsWidget extends BreadcrumbsArrayWidget
{
	public $controller=null;

	/**
	 * (non-PHPDoc)
	 * @see \CWidget::init()
	 */
	public function init()
	{
		if(!($this->controller instanceof \CController)) {
			$this->controller=\Yii::app()->getController();
		}

		parent::init();
	}
}