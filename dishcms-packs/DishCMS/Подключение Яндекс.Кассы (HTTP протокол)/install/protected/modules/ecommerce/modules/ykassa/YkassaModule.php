<?php
/**
 * Модуль Яндекс.Касса
 */
use common\components\helpers\HYii as Y;
use common\components\base\WebModule;

class YkassaModule extends WebModule
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
			'ykassa.models.*',
			'ykassa.behaviors.*',
			'ykassa.components.*',
		));		
	}
}
