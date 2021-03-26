<?php
namespace Kontur\Salestat;

use Bitrix\Main;

class Helper
{
    const CONFIG_MAIN='main';
    
    /**
     * Временный кэш конфигураций
     *
     * @var array
     */
    private static $configs=[];
    
    public static function getConfig($name, $returnContent=false)
    {
        if(!isset(static::$configs[$name])) {
            $config=[];

            $filename=dirname(__FILE__) . '/../config/' . $name . '.php';
            if(is_file($filename)) {
                if($returnContent) {
                    $config=file_get_contents($filename);
                }
                else {
                    $config=@include($filename);
                    if(!is_array($config)) {
                        $config=[];
                    }
                }
            }

            static::$configs[$name]=$config;
        }

        return static::$configs[$name];
    }

    public static function getConfigParam($name, $param, $default=null, $allowEmpty=true)
    {
        $config=static::getConfig($name);

        if(isset($config[$param]) && ($config[$param] || $allowEmpty)) {
            return $config[$param];
        }

        return $default;
    }

    public static function getSiteId()
    {
        return static::getConfigParam(self::CONFIG_MAIN, 'SITE_ID', 's1');
    }

    public static function getItemsIblockId()
    {
        return intval(static::getConfigParam(self::CONFIG_MAIN, 'ITEMS_IBLOCK_ID', static::getDefaultItemsIblockId(), false));
    }

    public static function getOffersIblockId()
    {
        return intval(static::getConfigParam(self::CONFIG_MAIN, 'OFFERS_IBLOCK_ID', static::getDefaultOffersIblockId(), false));
    }

    public static function getDefaultItemsIblockId()
    {
        $iblock=\CIBlock::GetList([], ['CODE'=>'catalog', 'CHECK_PERMISSIONS'=>'N', 'ACTIVE'=>'Y'])->Fetch();
        
        return isset($iblock['ID']) ? intval($iblock['ID']) : null;
    }
    
    public static function getDefaultOffersIblockId()
    {
        $iblock=\CIBlock::GetList([], ['CODE'=>'offers',  'CHECK_PERMISSIONS'=>'N', 'ACTIVE'=>'Y'])->Fetch();

        return isset($iblock['ID']) ? intval($iblock['ID']) : null;
    }

    public static function getCml2LinkProperty()
    {
        return static::getConfigParam(self::CONFIG_MAIN, 'CML2_LINK_PROPERTY', 'CML2_LINK');
    }
}