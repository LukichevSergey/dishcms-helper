<?php
/**
 * Simple breadcrumbs widget
 */
namespace menu\widgets\breadcrumbs\simpleBreadcrumbsWidget;

class SimpleWidget extends \CWidget
{
    /**
     * @var array breadcrumbs (array(url=>url, title=>title))
     */
	public $breadcrumbs = array();

	/**
	 * (non-PHPdoc)
	 * @see \menu\widgets\breadcrumbs\BaseBreadcrumbsWidget::run()
	 */
	public function run()
	{
		$this->render('simple', array('breadcrumbs'=>$this->breadcrumbs));
	}
}