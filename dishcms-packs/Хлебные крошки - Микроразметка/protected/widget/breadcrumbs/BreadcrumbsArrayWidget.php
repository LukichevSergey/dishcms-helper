<?php
/**
 * Array breadcrumbs widget
 */
class BreadcrumbsArrayWidget extends \CWidget
{
    /**
     * @var boolean подключать внутрении стили.
     */
	public $importStyles=true;

    /**
     * @var array breadcrumbs (array(url=>url, title=>title))
     */
	public $breadcrumbs = array();

	/**
	 * @var string название главной страницы сайта.
	 * Если не задано будет взято из \Yii::t('BreadcrumbsArrayWidget.main', 'homeTitle');
	 */
	public $homeTitle=null;

	/**
	 * @var boolean использовать шаблон с микроразметкой schema.org
	 */
	public $useSchema=true;

	/**
	 * @var string шаблон отображения. 
	 */
	public $view=null;

	/**
	 * (non-PHPDoc)
	 * @see \CWidget::init()
	 */
	public function init()
	{
		if($this->homeTitle===null) {
			$this->homeTitle=\Yii::t('BreadcrumbsArrayWidget.main', 'homeTitle');
		}

		parent::init();
	}

	/**
	 * (non-PHPdoc)
	 * @see \CWidget::run()
	 */
	public function run()
	{
		if($this->view===null) {
			$this->view=$this->useSchema ? 'schema_array' : 'array';
		}

		$this->render($this->view, array('breadcrumbs'=>$this->breadcrumbs));
	}
}