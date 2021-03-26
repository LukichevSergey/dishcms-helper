<?php
/**
 * Simple breadcrumbs widget
 */
namespace menu\widgets\breadcrumbs\simpleBreadcrumbsWidget;

use menu\widgets\breadcrumbs\BaseBreadcrumbsWidget;

class simpleBreadcrumbsWidget extends BaseBreadcrumbsWidget
{
	/**
	 * (non-PHPdoc)
	 * @see \menu\widgets\breadcrumbs\BaseBreadcrumbsWidget::run()
	 */
	public function run()
	{
		$this->render('default', array('breadcrumbs'=>$this->breadcrumbs));
	}
}