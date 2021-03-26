<?php
namespace Kontur\CheckPrice;

use Bitrix\Main;

class Helper
{
    /**
     * @var int тип цены по умолчанию
     */
    const DEFAULT_CATALOG_GROUP_ID=1;
    
    const CONFIG_MAIN='main';
    const CONFIG_PRICETAG='pricetag';
    const CONFIG_EMAILS='emails';

    /**
     * @var integer статус "Запущен"
     */
    const SNAP_STATUS_RUNNED=100;

    /**
     * @var integer статус "Завершен"
     */
    const SNAP_STATUS_DONE=200;

    /**
     * @var integer статус "Ошибка"
     */
    const SNAP_STATUS_ERROR=400;

    /**
     * @var int количество обрабатываемых товаров за шаг
     */
    const DEFAULT_SNAP_LIMIT=500;

    /**
     * @var int задержка в секундах между шагами
     */
    const DEFAULT_SNAP_DELAY=1;

    /**
     * Временный кэш конфигураций
     *
     * @var array
     */
    private static $configs=[];

    private static $isAgent=false;

    public static function isAgent($isAgent=null)
    {
    	if(is_bool($isAgent)) { 
    		static::$isAgent=$isAgent; 
    	}

    	return (bool)static::$isAgent;
    }

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

    public static function inGroup($groups=[])
    {
    	global $USER;
    	
    	$isEmpty=!(!empty($USER) && ($USER instanceof \CUser));
        if($isEmpty) {
	    	$_USER=@$USER;
            $USER=new \CUser;
            if(static::isAgent()) {
	            $USER->SetUserGroupArray($groups);
	        }
        }

        $inGroup=\CSite::InGroup($groups);

        if($isEmpty) {
       		if($_USER) { 
       			$USER=$_USER; 
       		}
            else { 
            	unset($USER); 
            }
        }

        return $inGroup;
    }

    public static function checkAccess($return=false)
    {        if(static::inGroup(static::getUsersGroupId())) {
            return true;
        }
        
        if(!$return) {
            throw new Main\SystemException('Доступ запрещен. Обратитесь к администратору сайта');
        }

        return false;
    }

    public static function checkAccessByCreateSnap($return=false)
    {
        if(static::inGroup(static::getCreateSnapUsersGroupId())) {
            return true;
        }
        
        if(!$return) {
            throw new Main\SystemException('Доступ запрещен. Обратитесь к администратору сайта');
        }

        return false;
    }

    public static function getUsersGroupId()
    {
        return static::getConfigParam(self::CONFIG_MAIN, 'USERS_GROUP_ID', []);
    }

    public static function getCreateSnapUsersGroupId()
    {
        return static::getConfigParam(self::CONFIG_MAIN, 'CREATE_SNAP_USERS_GROUP_ID', []);
    }

    public static function getSiteId()
    {
        return static::getConfigParam(self::CONFIG_MAIN, 'SITE_ID', 's1');
    }

    public static function getSnapLimit()
    {
        return (int)static::getConfigParam(self::CONFIG_MAIN, 'SNAP_LIMIT', self::DEFAULT_SNAP_LIMIT);
    }

    public static function getCatalogGroupId()
    {
        return (int)static::getConfigParam(self::CONFIG_MAIN, 'CATALOG_GROUP_ID', self::DEFAULT_CATALOG_GROUP_ID);
    }

    public static function getPriceTagListPageUrl()
    {
        return static::getConfigParam(self::CONFIG_PRICETAG, 'PRICETAG_LIST_PAGE_URL', '#', false);
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

    public static function getDiscountUserGroups()
    {
        return static::getConfigParam(self::CONFIG_MAIN, 'DISCOUNT_USER_GROUPS', []);
    }

    public static function getMailPrimaryAddresses()
    {
        $emails=[];

        $configEmails=explode("\n", (string)static::getConfig(self::CONFIG_EMAILS, true));
        if(!empty($configEmails)) {
            foreach($configEmails as $email) {
                $email=trim($email);
                if(!empty($email)) { 
                    $emails[]=$email;
                }
            }
        }

        return $emails;
    }

    public static function getOptimalPrice($id, $returnOnlyDiscountPrice=true)
    {
        $price=\CCatalogProduct::GetOptimalPrice($id, 1, static::getDiscountUserGroups(), 'N');

        if($returnOnlyDiscountPrice) {
            return $price['RESULT_PRICE']['UNROUND_DISCOUNT_PRICE'] ?? null;
        }
        else {
            return $price;
        }
    }

    /**
     * Отправка почтовых уведомлений с изменением цен
     *
     * @param int $snapId
     * @return bool
     */
    public static function sendPriceList($snapId)
    {
        global $APPLICATION, $USER;

        $to=static::getMailPrimaryAddresses();
        if(!empty($to)) {
            if($snap=SnapTable::getSnapById($snapId)) {
                $nextSnap=SnapTable::getList([
                    'filter'=>['STATUS'=>Helper::SNAP_STATUS_DONE, '<ID'=>$snap->getId()],
                    'order'=>['CREATE_TIME'=>'DESC'],
                    'limit'=>1
                ])->fetchObject();
                if($nextSnap) {
                    $fields=[
                        'EMAIL_TO' => array_shift($to),
                        'BCC' => implode(',', $to),
                        'PERIOD' => $nextSnap->getCreateTime()->format('d.m')
                            . ' / ' . $snap->getCreateTime()->format('d.m')
                        /* 
                        'PERIOD' => $nextSnap->getCreateTime()->format('d.m.Y')
                            . '<sup style="font-weight:normal;font-size:13px;">' . $nextSnap->getCreateTime()->format('H:i') . '</sup>'
                            . ' / ' . $snap->getCreateTime()->format('d.m.Y')
                            . '<sup style="font-weight:normal;font-size:13px;">' . $snap->getCreateTime()->format('H:i') . '</sup>'
                        /**/
                    ];
                
                    ob_start();
                    $APPLICATION->IncludeComponent('kontur:checkprice', 'pricelist', [
                        'SNAP_1'=>$snap->getId(),
                        'SNAP_2'=>$nextSnap->getId()
                    ], false);
                    $fields['PRICELIST']=ob_get_clean();

                    if(!empty($fields['PRICELIST'])) {
                        \CEvent::Send('KONTUR_CHECKPRICE_PRICELIST', static::getSiteId(), $fields, 'N');
                    }
                }
            }
        }
    }
}