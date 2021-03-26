<?php
/**
 * Класс помощник для работы с регионами
 */
namespace extend\modules\regions\components\helpers;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HRequest as R;
use common\components\helpers\HCache;
use extend\modules\regions\models\Region;
 
class HRegion
{
	use \common\traits\Singleton;
	
	/**
	 * @var string имя основного домена, на который, 
	 * в случае не нахождения региона, будет выполено перенаправление.
	 */
	public $baseDomain = false;
	
	/**
	 * @var boolean использовать данные основного региона для пустых значений.
	 * По умолчанию (false) использовать.
	 */
	public $useDefaultIfEmpty = false;
	
	/**
	 * @var boolean получать значения из оригинального атрибута
	 * для основного региона. По умолчанию (true) получать значение из
	 * оригинального атрибута, если он задан.
	 */
	public $enableFromOrigin = true;
	
	/**
	 * @var boolean получать значения из оригинального атрибута
	 * для региона. По умолчанию (true) получать значение из
	 * оригинального атрибута, если он задан.
	 */
	public $enableFromOriginByRegion = true;
	
	/**
	 * @var boolean использовать атрибут принудительной установки заданного значения
	 * даже если оно пустое. По умолчанию (false) использовать.
	 */
	public $enableForced = false;
	
	/**
	 * @var string путь к настройкам данного класса в основной конфигурации приложения.
	 * По умолчанию:
	 * "extend.modules.regions.components.helpers.HRegion"
	 */
	public $configPath='extend.modules.regions.components.helpers.HRegion';
	
	/**
	 * @access protected
	 * @var array свойства, которые разрешены для изменения в конфигурации приложения.
	 */
	protected $configProps=[
		'baseDomain', 
		'useDefaultIfEmpty', 
		'enableFromOrigin', 
		'enableFromOriginByRegion',
		'enableForced'
	];
	
	/**
	 * @access protected
	 * @var \extend\modules\regions\models\Region модель текущего региона. 
	 */
	protected $region = false;
	
	/**
	 * @access protected
	 * @var \extend\modules\regions\models\Region модель региона по умолчанию. 
	 */
	protected $defaultRegion = false;
	
	/**
	 * @access protected
	 * @var boolean инициализация выполена.
	 */
	protected $isInitialized = false;
	
	/**
	 * @access private
	 * @var array кэш
	 */
	private $_cache = [];
	
	/**
	 * @access
	 * @var array кэш, имеют ли запрошенные контроллеры поведение
	 * \extend\modules\regions\behaviors\AdminControllerBehavior
	 * Это кэш для метода HRegion::hasAdminControllerBehavior()
	 */
	private $_cacheHasBehavior = [];
	
	/**
	 * Инициализация
	 */
	public function initialize()
	{
		if(!$this->isInitialized) {
			Y::module('extend.regions');
			
			foreach($this->configProps as $prop) {
				$this->{$prop} = Y::param($this->configPath . '.' . $prop, $this->{$prop});
			}
			$this->isInitialized = true;
		}
	}
	
	/**
	 * Получить регион.
	 * @param integer|false $id идентификатор региона. По умолчанию (false) текущий.
	 * @param boolean $replace заменить текущий регион. По умолчанию (false) не заменять.
	 * @return \extend\modules\regions\models\Region|false модель региона или false 
	 * если регион не найден.
	 */
	public function region($id=false, $replace=false)
	{
		$this->initialize();
		
		if($id) {
			if(isset($this->_cache[$id])) {
				$model = $this->_cache[$id];
			}
			else {
				$model = Region::model()->activly()->utcache(HCache::YEAR)->findByPk($id);
			}
			
			if($replace) {
				$this->region = $model;
			}
		}
		elseif($this->region) {
			$model = $this->region;
		}
		else {
			$this->region = Region::model()->activly()->findByAttributes(['domain'=>R::r()->serverName]);
			$model = $this->region;
		}
		
		return $model;
	}
	
	/**
	 * Получить ID текущего региона
	 * @return integer|false
	 */
	public function getId()
	{
		$this->initialize();
		
		if($this->region()) {
			return $this->region()->id;
		}
		
		return false;
	} 
	
	/**
	 * Получить имя домена текущего региона
	 * @param integer|false $id идентификатор региона. По умолчанию (false) текущий.
	 * @return string|false
	 */
	public function getDomain($id=false)
	{
		if($this->region($id)) {
			return $this->region($id)->domain;
		}
		
		return false;
	}
	
	/**
	 * Перенаправление на основной домен
	 * @param integer $code код ответа. По умолчанию 302.
	 * @param integer|false $id идентификатор региона. По умолчанию (false) текущий.
	 */
	public function redirect($code=302, $id=false)
	{
		if($id) {
			$domain = $this->getDomain($id);
		}
		else {
			$domain = $this->baseDomain;
		}
		
		if(!$domain) {
			R::e404();
		}
		
		R::r()->redirect('http://'.$domain, true, $code);
	}
	
	/**
	 * Получить постфикс региона
	 * @param integer|false $id идентификатор региона. По умолчанию (false) текущий.
	 * @return string если постфикс не найден, возвращается пустая строка.
	 */
	public function getPostfix($id=false) 
	{
		if($this->region($id)) {
			return $this->region($id)->postfix;
		}
		
		return '';
	}

	/**
	 * Получить название региона
	 * @param integer|false $id идентификатор региона. По умолчанию (false) текущий.
	 * @return string если наименование региона не найдено, возвращается пустая строка.
	 */
	public function getTitle($id=false)
	{
		if($this->region($id)) {
			return $this->region($id)->title;
		}
		
		return '';
	}
	
	/**
	 * Получить регион по умолчанию
	 * @return \extend\modules\regions\models\Region
	 */
	public function getDefaultRegion()
	{
		if(!$this->defaultRegion) {
			$this->defaultRegion=Region::model()->getDefaultRegion();
		}
		
		return $this->defaultRegion;
	}
	
	/**
	 * Регион является регионом по умолчанию.
	 * @param integer|false $id идентификатор региона. По умолчанию (false) текущий.
	 * @return bool 
	 */
	public function isDefault($id=false)
	{
		return (bool)$this->region($id)->is_default;
	}
	
	/**
	 * Установка региона в COOKIE.
	 * @param integer|false|null $regionId идентификатор региона. 
	 * По умолчанию (false) будет установлен текущий.
	 * Если будет передано значение (null) переменная будет удалена из COOKIE.
	 * @param string|false $cookieName имя переменной в COOKIE. 
	 * По умолчанию (false) будет получено из 
	 * \extend\modules\regions\RegionsModule::$regionCookieName
	 * @param integer $lifeTime продолжительность сессии (сек). 
	 * По умолчанию 0(нуль) до закрытия браузера.
	 * @param string $path путь для COOKIE. По умолчанию "/".
	 */
	public function setCookie($regionId=false, $cookieName=false, $lifeTime=0, $path='/')
	{
		if(!$cookieName) {
			$cookieName=Y::module('extend.regions')->regionCookieName;
		}
		
		if($regionId === null) {
			setcookie($cookieName, false);
			unset($_COOKIE[$cookieName]);			
		}
		else {
			setcookie($cookieName, $regionId, $lifeTime, $path);
			$_COOKIE[$cookieName]=$regionId;
		}
	}
	
	/**
	 * Текущий контроллер содержит поведение \extend\modules\regions\behaviors\AdminControllerBehavior
	 * @param string|false $name имя поведения. По умолчанию (false) будет определено автоматически. 
	 * @return boolean
	 */
	public function hasAdminControllerBehavior($name=false)
	{
		if(!Y::controller()) {
			return false;
		}
		
		$id=Y::controller()->id;
		if(!A::existsKey($id, $this->_cacheHasBehavior)) {
			if($name) {
				$this->_cacheHasBehavior[$id] = (bool)Y::controller()->asa($name);
			}
			else {
				$this->_cacheHasBehavior[$id] = false;
				foreach(Y::controller()->behaviors() as $name=>$behavior) {
					if(is_array($behavior)) {
						if(A::get($behavior, 'class') == '\extend\modules\regions\behaviors\AdminControllerBehavior') {
							$this->_cacheHasBehavior[$id] = true;
							break;
						}
					}
					elseif($behavior == '\extend\modules\regions\behaviors\AdminControllerBehavior') {
						$this->_cacheHasBehavior[$id] = true;
						break;
					}
				}
			}
		}
		
		return $this->_cacheHasBehavior[$id];
	}
}
