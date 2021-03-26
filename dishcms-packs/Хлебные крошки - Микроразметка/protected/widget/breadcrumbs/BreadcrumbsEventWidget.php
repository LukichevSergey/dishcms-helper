<?php
/**
 * Хлебные крошки для модели Event
 */
Yii::import('widget.breadcrumbs.BaseBreadcrumbsWidget');

class BreadcrumbsEventWidget extends BaseBreadcrumbsWidget
{
	/**
	 * @var string название главной страницы новостей.
	 * Если не задано будет взято из \Yii::t('BreadcrumbsArrayWidget.main', 'eventsTitle');
	 */
	public $eventsTitle=null;

	public $eventsAlias='news';

	/**
	 * @var \Event|NULL модель новости. Если не передана, то отображаются крошки для страницы списка.
	 */
	public $model=null;

	/**
	 * (non-PHPDoc)
	 * @see \CWidget::init()
	 */
	public function init()
	{
		if($this->eventsTitle===null) {
			$this->eventsTitle=\Yii::t('BreadcrumbsArrayWidget.main', 'eventsTitle');
		}

		parent::init();
	}

	/**
	 * (non-PHPdoc)
	 * @see \CWidget::run()
	 */
	public function run()
	{
		$this->breadcrumbs[]=array('url'=>'/'.$this->eventsAlias, 'title'=>$this->eventsTitle);

		if($this->model !== null) {
			$this->breadcrumbs[]=array('url'=>$this->controller->createUrl('site/event', array('id'=>$this->model->id)), 'title'=>$this->model->title);
		}

		parent::run();
	}
}