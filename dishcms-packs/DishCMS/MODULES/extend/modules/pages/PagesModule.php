<?php
/**
 * Модуль Страницы.
 */
namespace extend\modules\pages;

use common\components\helpers\HYii as Y;
use common\components\base\WebModule;

class PagesModule extends WebModule
{
	/**
	 * (non-PHPdoc)
	 * @see CModule::init()
	 */
	public function init()
	{
		parent::init();
		
		// $this->assetsBaseUrl=Y::publish($this->getAssetsBasePath());

		$this->setImport(array(
			'pages.models.*',
			'pages.behaviors.*',
			'pages.components.*',
		));		
	}
}