<?php
namespace accounts\components\helpers;

use common\components\helpers\HArray as A;
use common\components\helpers\HRequest as R;
use common\components\helpers\HHash;
use common\components\helpers\HEvent;
use settings\components\helpers\HSettings;
use crud\models\ar\accounts\models\Account;

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
        if(static::isAuth()) {
            if($event=HEvent::raise('onAccountGetActiveWebUser')) {
                if(($webUser=A::get($event->params, 'webUser')) && $webUser->user_id) {
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
            return A::get($event->params, 'success', false);
        }
        
        return false; 
    }
    
    /**
     * Авторизация пользователя
     * @param string|Account $phone номер телефона или объект аккаунта
     * @param string $password пароль
     * @param boolean $rememberMe запомнить авторизацию. 
     * По умолчанию (FALSE) не запоминать.
     * @return boolean
     */
    public static function login($phone, $password, $rememberMe=false)
    {
        if(!($phone instanceof Account)) {
            $account=new Account('auth');
            $account->phone=$phone;
            $account->password=$password;
            $account->remember_me=$rememberMe;
        }
        else {
            $account=$phone;
        }
        
        if($account->validate()) {
            if($event=HEvent::raise('onAccountGetUserIdentity', compact('account'))) {
                if($identity=A::get($event->params, 'identity')) {
                    if($account->accountBehavior->authByUserIdentity($identity)) {
                        if($event=HEvent::raise('onAccountAuthIdentitySuccessed', ['identity'=>$identity, 'duration'=>$account->remember_me ? 3600*24*365 : 0])) {
                            if(A::get($event->params, 'success', false)) {
                                return true;
                            }
                        }
                    }
                }
            }
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
            return A::get($event->params, 'success', false);
        }
        
        return false;
    }
}