<?php
/**
 * Модуль интеграции с ПЭК
 */
use common\components\helpers\HYii as Y;
use common\components\base\WebModule;

class PecomModule extends WebModule
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
			'pecom.components.*',
		));		
	}
}
