<?php
/**
 * Поведение для контроллера раздела администрирования.
 * 
 */
namespace extend\modules\regions\behaviors;

use common\components\helpers\HYii as Y;
use extend\modules\regions\components\helpers\HRegion;
use extend\modules\regions\behaviors\RegionAttributeBehavior;

class AdminControllerBehavior extends \CBehavior
{
	/**
	 * @var string имя переменной в COOKIE.
	 * По умолчанию (false) будет использована заданная 
	 * в модуле \extend\modules\regions\RegionsModule::$regionCookieName
	 */
	public $cookieName = false;

	/**
	 * @var string имя параметра в запросе, в котором передается ID региона.
	 */
	public $requestParam='rid';
	
	/**
	 * (non-PHPdoc)
	 * @see CBehavior::attach()
	 */
	public function attach($owner)
	{
		parent::attach($owner);
		
		if(!$this->cookieName) {
			$this->cookieName=Y::module('extend.regions')->regionCookieName;
		}
		
		if($rid=Y::request()->getParam($this->requestParam)) {
			HRegion::i()->setCookie($rid, $this->cookieName);
		}
		
		if(isset($_COOKIE[$this->cookieName])) {
			if(!HRegion::i()->region($_COOKIE[$this->cookieName], true)) {
				HRegion::i()->setCookie(null, $this->cookieName);
			}
		}
		
		RegionAttributeBehavior::checkRegionHashField();
	}
}

