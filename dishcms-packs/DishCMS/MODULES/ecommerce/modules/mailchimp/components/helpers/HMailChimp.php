<?php
/**
 * Класс-помощник для модуля MailChimp
 * 
 */
namespace mailchimp\components\helpers;

use common\components\helpers\HArray as A;
use common\components\helpers\HFile;
use settings\components\helpers\HSettings;
use DOrder\models\DOrder;

class HMailChimp
{
    const SUBSCRIBER_STATUS_SUBSCRIBED='subscribed';
    const SUBSCRIBER_STATUS_UNSUBSCRIBED='unsubscribed';
    const SUBSCRIBER_STATUS_CLEANED='cleaned';
    const SUBSCRIBER_STATUS_PENDING='pending';
    
    private static $settings;
    private static $initialized=false;
    
    protected static function init()
    {
        if(!static::$initialized) {
            include_once HFile::path([\Yii::getPathOfAlias('mailchimp.vendors'), 'Mailchimp-API-3.0', 'src' , 'mailchimpRoot.php']);
            $initialized=true;
        }
    }
    
    public static function settings()
    {
        if(!static::$settings) {
            static::$settings=HSettings::getById('shop');
        }
        return static::$settings;
    }
    
    public static function getKey()
    {
        if(static::settings()->mailchimp_key) {
            return static::settings()->mailchimp_key;
        }
        return Y::param('mailchimp.key');
    }
    
    public static function getListId()
    {
        if(static::settings()->mailchimp_default_list_id) {
            return static::settings()->mailchimp_default_list_id;
        }        
        return false;
    }
    
    public static function mailchimp()
    {
        static::init();
        
        $mailchimp=new \Mailchimp(static::getKey());
        
        return $mailchimp;
    }
    
    /**
     * Добавить клиента в список
     * @link http://developer.mailchimp.com/documentation/mailchimp/reference/lists/members/
     * @param string $email  email клиента
     * @param string $status статус. По умолчанию "pending". 
     * Разрешенные значения subscribed, unsubscribed, cleaned, pending).
     * @param array $extra дополнительные параметры клиента
     * @param array $listId идентификатор списка. По умолчанию указанный в настройках.
     */
    public static function addListMember($email, $status='pending', $extra=[], $listId=false)
    {
        if($listId === false) {
            $listId=static::getListId();
        }
        
        if(!$listId) {
            return false;
        }
        
        return static::mailchimp()->lists($listId)->members()->POST(A::m([
            'email_address'=>$email,
            'status'=>$status
        ], $extra));
    }
    
    public static function addListMemberByOrderId($orderId, $status='pending', $extra=[], $listId=false)
    {
        if($order=DOrder::model()->findByPk($orderId)) {
            if($email=A::rget($order->getCustomerData(), 'email.value')) {
                return static::addListMember($email, $status, $extra, $listId);
            }
        }
        return false;
    }
}
