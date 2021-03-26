<?php
/**
 * Модуль Регионы
 */
namespace extend\modules\regions;

//use common\components\helpers\HYii as Y;
use common\components\base\WebModule;

class RegionsModule extends WebModule
{
	/**
	 * @var string имя переменной в COOKIE, которая
	 * хранит текущий регион (при внутренней смене региона).
	 */
	public $regionCookieName='current_region';
	
	/**
	 * (non-PHPdoc)
	 * @see CModule::init()
	 */
	public function init()
	{
		parent::init();
		
		// $this->assetsBaseUrl=Y::publish($this->getAssetsBasePath());

		$this->setImport(array(
			'extend.modules.regions.models.*',
			'extend.modules.regions.behaviors.*',
			'extend.modules.regions.components.*',
		));		
	}
}