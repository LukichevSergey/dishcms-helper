<?php
/**
 * Модуль
 */
namespace extend\modules\buildings;

use common\components\helpers\HYii as Y;
use common\components\base\WebModule;

class BuildingsModule extends WebModule
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
			'extend.modules.buildings.models.*',
			'extend.modules.buildings.behaviors.*',
			'extend.modules.buildings.components.*',
		));		
	}
}