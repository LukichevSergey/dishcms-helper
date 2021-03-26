<?php
/**
 * Модуль Слайдер
 */
namespace extend\modules\slider;

use common\components\helpers\HYii as Y;
use common\components\base\WebModule;

class SliderModule extends WebModule
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
			'slider.models.*',
			'slider.behaviors.*',
			'slider.components.*',
		));		
	}
}