<?php
/**
 * Модуль Банки
 */
namespace extend\modules\banks;

use common\components\helpers\HYii as Y;
use common\components\base\WebModule;

class BanksModule extends WebModule
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
			'application.modules.extend.modules.banks.models.*',
			'application.modules.extend.modules.banks.components.*',
		));		
	}
}