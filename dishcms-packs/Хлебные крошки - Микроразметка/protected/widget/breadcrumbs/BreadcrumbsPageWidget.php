<?php
/**
 * Хлебные крошки для модели Page
 */
Yii::import('widget.breadcrumbs.BaseBreadcrumbsWidget');

class BreadcrumbsPageWidget extends BaseBreadcrumbsWidget
{
	/**
	 * @var \Page|NULL модель страницы.
	 */
	public $model=null;

	/**
	 * (non-PHPdoc)
	 * @see \CWidget::run()
	 */
	public function run()
	{
		if(!$this->model !== null) {
			$this->breadcrumbs[]=array('url'=>$this->controller->createUrl('site/page', array('id'=>$this->model->id)), 'title'=>$this->model->title);
		}

		parent::run();
	}
}