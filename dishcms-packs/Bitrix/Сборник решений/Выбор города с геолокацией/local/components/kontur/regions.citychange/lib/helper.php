<?
/**
 * init.php
 * 
 * require_once $_SERVER['DOCUMENT_ROOT'] . '/local/components/kontur/regions.citychange/lib/helper.php';
 * KRCCHelper::i()->init([
 *    'IBLOCK_ID'=><идентификатор инфоблока регионов>,
 *    'COOKIE_KEY'=><имя переменной текущего региона в COOKIE>,
 *    'PROPERTY_IS_DEFAULT_CODE'=><код свойства региона по умолчанию>,
 *    'DISABLE_GEOIP'=>'Y' // по умолчанию 'N' - определение геолокации методами Битрикса включено.
 * ]);
 *  
 */
use Bitrix\Main\Loader;
use Bitrix\Main\Service\GeoIp;
use Bitrix\Iblock\ElementTable;
use Bitrix\Sale\Location\LocationTable;
use Kontur\Core\Main\Tools\Data;

class KRCCHelper
{
    private static $instance=null; 
    
    private $iblockId=null;
    private $cookieKey=null;
    private $propIsDefaultCode=null;
    private $disableGeoIp='N';
    private $activeCity=null;
    private $cache=[];
    
    public static function i()
    {
        if(!static::$instance) {
            static::$instance=new static;
        }
                
        return static::$instance;
    }
    
    public function init($params)
    {
        Loader::includeModule('iblock');
        Loader::includeModule('sale');
        
        $this->iblockId=Data::get($params, 'IBLOCK_ID');
        $this->cookieKey=Data::get($params, 'COOKIE_KEY');
        $this->propIsDefaultCode=Data::get($params, 'PROPERTY_IS_DEFAULT_CODE');
        $this->disableGeoIp=Data::get($params, 'DISABLE_GEOIP', 'N');
    }
    
        
    public function getLocation($id)
    {
        return LocationTable::getList(array(
            'filter' => array(
                (is_array($id)?'':'=').'ID' => $id, 
                '=PARENTS.NAME.LANGUAGE_ID' => LANGUAGE_ID,
                '=PARENTS.TYPE.NAME.LANGUAGE_ID' => LANGUAGE_ID,
            ),
            'select' => array(
                'I_ID' => 'PARENTS.ID',
                'I_CODE' => 'PARENTS.CODE',
                'I_NAME_RU' => 'PARENTS.NAME.NAME',
                'I_TYPE_CODE' => 'PARENTS.TYPE.CODE',
                'I_TYPE_NAME_RU' => 'PARENTS.TYPE.NAME.NAME'
            ),
            'order' => array(
                'PARENTS.DEPTH_LEVEL' => 'asc'
            )
        ))->fetchAll();
    }
    
    public function getCookieCityCode()
    {
        if($this->cookieKey) {
            return Data::get($_COOKIE, $this->cookieKey);
        }
        return null;
    }
    
    public function getActiveCity($refresh=false)
    {
        if($refresh || !$this->activeCity) {
            if(!$refresh && ($this->activeCity === false)) {
                return false;
            }
            else {
                $cityCode=$this->getCookieCityCode();
                if(empty($cityCode)) {
                    if($this->iblockId && ($this->disableGeoIp != 'Y')) {
                        GeoIp\Manager::useCookieToStoreInfo(true);
                        $ipAddress='5.128.156.28';//GeoIp\Manager::getRealIp();
                        $getIpResult=GeoIp\Manager::getDataResult($ipAddress, 'ru', ['cityName']);
                        $geoData=$getIpResult->getGeoData();
                        if($geoData->cityName) {
                            $cities=ElementTable::getList([
                                'filter'=>['=IBLOCK_ID'=>$this->iblockId],
                                'select'=>['NAME', 'CODE']
                            ])->fetchAll();
                            
                            $lastPercSimText=0;
                            foreach($cities as $city) {
                                similar_text($city['NAME'], $geoData->cityName, $perc);
                                if($perc > $lastPercSimText) {
                                    $cityCode=$city['CODE'];
                                    $lastPercSimText=$perc;
                                }
                            }
                        }
                    }
                    
                    if(!empty($cityCode)) {
                        $filter=['=CODE'=>$cityCode];
                    }
                    elseif($this->propIsDefaultCode) {
                        $filter=["=PROPERTY_{$this->propIsDefaultCode}_VALUE"=>'Y'];
                    }
                }
                else {
                    $filter=['=CODE'=>$cityCode];
                }
                
                $this->activeCity=$this->getCity($filter, ['ID', 'IBLOCK_ID', 'CODE', 'NAME', 'PROPERTY_*'], !empty($cityCode));
                
                if(!$this->activeCity) {
                    $this->activeCity=false;
                }
            }
        }
        
        return $this->activeCity;
    }    
    
    public function get($propertyCode, $default=null, $forcyEmpty=false)
    {
        return $this->getByCityCode(Data::get($this->getActiveCity(), 'CODE'), $propertyCode, $default);
    }
    
    public function getByCityCode($cityCode, $propertyCode, $default=null)
    {
        $cacheId="{$cityCode}_PROPERTY_{$propertyCode}_VALUE";
        if(array_key_exists($cacheId, $this->cache)) {
            return $this->cache[$cacheId];
        }
        elseif($cityCode && $propertyCode && $this->iblockId) {
            if($city=$this->getCity(['=CODE'=>$cityCode], ['ID', 'IBLOCK_ID', 'PROPERTY_' . $propertyCode])) {
                $value=$city["PROPERTY_{$propertyCode}_VALUE"];
                if(!empty($value) || $forcyEmpty) {
                    if(!empty($value['TEXT'])) $value=$value['TEXT'];
                    $this->cache[$cacheId]=$value;
                    return $value;
                }
            }
        }
        
        $value=$this->getDefault($default, [$cityCode]);
        $this->cache[$cacheId]=$value;
        
        return $value;
    }
    
    protected function getDefault($default=null, $params=[])
    {
        if(is_callable($default)) {
            ob_start();
            call_user_func($default, $params);
            return ob_get_clean();            
        }
        
        return $default;
    }
    
    protected function getCity($filter=[], $select=['ID', 'IBLOCK_ID', 'NAME', 'CODE'], $checkOne=true)
    {
        $filter['IBLOCK_ID']=$this->iblockId;
        
        $rs=\CIBlockElement::GetList(['SORT'=>'ASC'], $filter, false, false, $select);
        
        if(!$checkOne || ($rs->SelectedRowsCount() === 1)) {
            return $rs->Fetch();
        }
        
        return null;
    }
}
