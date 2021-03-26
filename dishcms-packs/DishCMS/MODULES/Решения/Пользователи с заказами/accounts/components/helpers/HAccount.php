<?php
namespace accounts\components\helpers;

use common\components\helpers\HArray as A;
use common\components\helpers\HRequest as R;
use common\components\helpers\HHash;
use common\components\helpers\HEvent;
use common\components\helpers\HDb;
use settings\components\helpers\HSettings;
use crud\models\ar\accounts\models\Account;
use common\ext\email\components\helpers\HEmail;

/**
 * Класс-помощник для модуля "Аккаунты"
 *
 */
class HAccount
{
    /**
     * Секретный ключ по умолчанию для повышения безопасности
     * @var string
     */
    const SECRET_KEY='A3(B{pD=+Xvt*bW@N5"bT>FQ';
    
    /**
     * Идентификатор успешного сообщения
     * @var string
     */
    const FLASH_SUCCESS='accounts_success';
    
    /**
     * Идентификатор сообщения об ошибке
     * @var string
     */
    const FLASH_FAIL='accounts_fail';
    
    /**
     * Объект активного авторизованного аккаунта
     * @var \crud\models\ar\accounts\models\Account|null
     */
    private static $account=null;
    
    /**
     * Получить объект настроек модуля
     * @return \accounts\models\AccountSettings
     */
    public static function settings()
    {
        return HSettings::getById('accounts');
    }
    
    /**
     * Получить объект активного авторизованного аккаунта
     * @param boolean $reload перезагрузить объект аккаунта
     * @return \crud\models\ar\accounts\models\Account|null
     */
    public static function account($reload=false)
    {
        try {
            if(static::isAuth()) {
                if($event=HEvent::raise('onAccountGetActiveWebUser')) {
                    if(($webUser=$event->getParam('webUser')) && $webUser->user_id) {
                        if($reload || (static::$account === null) || ($webUser->user_id !== static::$account->id)) {
                            if(static::$account=Account::modelById($webUser->user_id)) {
                                static::$account->setScenario('account');
                            }
                            else {
                                static::$account=null;
                            }
                        }
                        return static::$account;
                    }
                }
            }
        } 
        catch(\Exception $e) {
            
        }
        
        return null;
    }
    
    /**
     * Получить значение секретного ключа
     * @return string
     */
    public static function secretKey()
    {
        $secretKey=static::settings()->secret_key;
        
        if(empty($secretKey)) {
            $secretKey=self::SECRET_KEY;
        }
        
        return $secretKey;
    }
    
    /**
     * Зашифровать данные
     * @param mixed $data данные для шифрования
     * @return string
     */
    public static function crypt($data)
    {
        return HHash::srEcrypt($data, static::secretKey());
    }
    
    /**
     * Расшифровать данные
     * @param string $data зашифрованные данные
     * @param boolean $assoc вернуть данные ассоциативным массивом
     * @return mixed
     */
    public static function decrypt($data, $assoc=false)
    {
        return HHash::srDecrypt($data, static::secretKey(), $assoc);
    }
    
    /**
     * Сгенерированить хэш-строку кода подтверждения
     * @param crud\models\ar\accounts\models\Account $account
     * @return string
     */
    public static function getCryptCode($account)
    {
        return static::crypt(['code'=>$account->confirm_code, 'id'=>$account->id, 'check'=>$account->check_code]);
    }
    
    /**
     * Получить объект аккаунта из хэш-строки кода подтверждения
     * @param string $code хэш-строка кода подтверждения
     * @param boolean $e404 бросать исключение 404 ошибки, 
     * если аккаунт не найден. По умолчанию (FALSE) не бросать. 
     * @return crud\models\ar\accounts\models\Account|null
     */
    public static function getAccountByCryptCode($code, $e404=false)
    {
        if(!empty($code) && ($data=static::decrypt($code, true))) {
            if(is_array($data) && !empty($data['id']) && !empty($data['code']) && !empty($data['check'])) {
                $account=Account::model()->findByAttributes(['id'=>$data['id'], 'confirm_code'=>$data['code'], 'check_code'=>$data['check']]);
            }
        }
        
        if($account instanceof Account) {
            return $account;
        }
        
        if($e404) {
            R::e404();
        }
        
        return null;
    }
    
    /**
     * Пользователь авторизован
     * @return bool
     */
    public static function isAuth()
    {
        if($event=HEvent::raise('onAccountIsAuth')) {
            return $event->getParam('success');
        }
        
        return false; 
    }
    
    /**
     * Авторизация для раздела администрирования по объекту Account
     * @param \crud\models\ar\accounts\models\Account $account
     * @return boolean
     */
    public static function loginByAdmin(&$account)
    {
        \Yii::import('admin.models.LoginForm');
        \Yii::import('admin.components.UserIdentity');
        
        $adminLoginForm=new \LoginForm();
        $adminLoginForm->username=$account->email;
        $adminLoginForm->password=$account->password;
        $adminLoginForm->rememberMe=$account->remember_me;
        
        if($adminLoginForm->validate() && $adminLoginForm->login()) {
            //\Yii::app()->user->setState('role', \Yii::app()->user->role);
            return true;
        }
        
        return false;
    }
    
    /**
     * Авторизация по объекту Account
     * @param \crud\models\ar\accounts\models\Account $account
     * @return boolean
     */
    public static function loginByAccount(&$account)
    {
        if($account->validate()) {
            if($event=HEvent::raise('onAccountGetUserIdentity', compact('account'))) {
                if($identity=$event->getParam('identity')) {
                    if($accountByIdentity=$account->accountBehavior->getAccountByIdentity($identity)) {
                        if($accountByIdentity->isWaitModeration()) { 
                            $account->addError('email', 'Ваш регистрационная информация находится на модерации.'); 
                        }
                        elseif(!$accountByIdentity->published) { 
                            $account->addError('email', 'Ваш доступ временно заблокирован.'); 
                        }
                        else {
                            $identity->setState('user_id', $accountByIdentity->id);
                            $identity->setState('role', $accountByIdentity->role);
                            $identity->errorCode=0;
                            $accountByIdentity->updateLoginTime();
                            if($event=HEvent::raise('onAccountLogin', ['identity'=>$identity, 'duration'=>$account->remember_me ? 3600*24*365 : 0])) {
                                if($event->getParam('success')) {
                                    return true;
                                }
                            }
                        }
                    }
                }
            }
        }
        
        if(!$account->hasErrors()) {
            $account->addError('email', 'Указан неверный e-mail или пароль.');
        }
        
        return false;
    }
    
    /**
     * Выход пользователя
     * @return bool
     */
    public static function logout()
    {
        if($event=HEvent::raise('onAccountLogout')) {
            return (bool)$event->getParam('success');
        }
        
        return false;
    }
    
    public static function getOrders()
    {
        if(HAccount::account()) {
            return HAccount::account()->getRelated('orders');
        }
        
        return null;
    }
    
    /**
     * Получить товары, отмеченные как "Избранный товар"
     */
    public static function getFavoriteProducts($criteria=null)
    {
        if(HAccount::account()) {
            if($favoriteProducts=HAccount::account()->getRelated('favoriteProducts')) {
                $criteria=HDb::criteria($criteria);
                $criteria->addInCondition('id', array_keys($favoriteProducts));
                if(!$criteria->order) {
                    $criteria->order='FIND_IN_SET(`id`, :orderIdSet)';
                    $criteria->params['orderIdSet']=implode(',', array_keys($favoriteProducts));
                }
                
                return \Product::model()->resetScope()->findAll($criteria);
            }
        }
        
        return null;
    }
 
    /**
     * Товар отмечен как "Избранный товар"
     * @param int $id идентификатор товара 
     */
    public static function isFavoriteProduct($id)
    {
        if(HAccount::account()) {
            if($favoriteProducts=HAccount::account()->getRelated('favoriteProducts')) {
                return isset($favoriteProducts[(int)$id]);
            }
        }
        
        return false;
    }
    
    /**
     * Пользователь является оптовым покупателем
     * @param Account|null $account если не передан, будет определено для текущего
     * @return boolean
     */
    public static function isWholesaleBuyer($account=null)
    {
        if($account === null) {
            $account=static::account();
        }
        
        if($account) {
            return ($account->role === Account::ROLE_WHOLESALE_BUYER);
        }
        
        return false;
    }
}