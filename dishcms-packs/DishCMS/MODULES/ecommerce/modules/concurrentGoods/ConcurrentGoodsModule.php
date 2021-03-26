<?php
/**
 * Модуль
 */
namespace ecommerce\modules\concurrentGoods;

use common\components\helpers\HYii as Y;
use common\components\base\WebModule;

class ConcurrentGoodsModule extends WebModule
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
			'ecommerce.modules.concurrentGoods.models.*',
			'ecommerce.modules.concurrentGoods.behaviors.*',
			'ecommerce.modules.concurrentGoods.components.*',
		));		
	}
}