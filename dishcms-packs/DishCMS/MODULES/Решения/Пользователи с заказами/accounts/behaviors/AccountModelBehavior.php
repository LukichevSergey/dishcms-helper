<?php
namespace accounts\behaviors;

use common\components\helpers\HArray as A;
use common\components\helpers\HDb;
use common\components\helpers\HHash;
use accounts\components\helpers\HAccount;
use crud\models\ar\accounts\models\Account;

/**
 * Поведение для модели "Аккаунт"
 *
 */
class AccountModelBehavior extends \CBehavior
{
    /**
     * Повторение пароля
     * @var string
     */
    public $repassword;
    
    /**
     * Предыдущий пароль
     * @var string
     */
    public $lastpassword;
        
    /**
     * {@inheritDoc}
     * @see \CBehavior::events()
     */
    public function events()
    {
        return [
            'onBeforeValidate'=>'beforeValidate',
            'onBeforeSave'=>'beforeSave',
            'onAfterSave'=>'afterSave'
        ];
    }
    
    /**
     * Правила валидации
     * @see \CActiveRecord::rules()
     * @return []
     */
    public function rules()
    {
        $rules=[
            ['email', 'email'],
            ['comment', 'length', 'max'=>255],
            
            // registration
            ['name, lastname, email', 'required', 'on'=>'registration'],
            ['email', 'unique', 'message'=>'Указанный Вами e-mail уже зарегистрирован.', 'on'=>'registration'],
            ['name, lastname', '\common\components\validators\NameValidator', 'on'=>'registration'],
            ['password', 'required', 'on'=>'registration'],
            ['password', 'length', 'min'=>6, 'max'=>20, 'on'=>'registration'],
            // ['password', '\common\components\validators\StrongPasswordValidator', 'on'=>'registration'],
            ['repassword', 'compare', 'compareAttribute'=>'password', 'message'=>'Пароли не совпадают', 'on'=>'registration'],
            ['privacy', 'required', 'requiredValue'=>1, 'message'=>'Вы не подтвердили свое согласие на обработку персональных данных.', 'on'=>'registration'],
            ['privacy', 'boolean', 'on'=>'registration'],
            ['is_wholesale', 'boolean', 'on'=>'registration'],
            // ['phone', 'safe', 'on'=>'registration'],
            // ['captcha', 'ext.reCaptcha2.SReCaptchaValidator', 'secret' => \Yii::app()->params['reCaptcha2PrivateKey'], 'message' => 'Не пройдена проверка "Я не робот"', 'on'=>'registration, registration_with_coupon'],
            
            // auto_registration
            ['email', 'required', 'on'=>'auto_registration'],
            ['email', 'unique', 'message'=>'Указанный Вами e-mail уже зарегистрирован.', 'on'=>'auto_registration'],
            ['name, lastname, phone', 'safe', 'on'=>'auto_registration'],
            
            // crud_create
            ['name, email', 'required', 'on'=>'crud_create'],
            ['password, repassword', 'required', 'on'=>'crud_create'],
            ['password', 'length', 'min'=>6, 'max'=>20, 'on'=>'crud_create'],
            // ['password', '\common\components\validators\StrongPasswordValidator', 'on'=>'crud_create'],
            ['name, lastname', '\common\components\validators\NameValidator', 'on'=>'crud_create'],
            ['repassword', 'compare', 'compareAttribute'=>'password', 'message'=>'Passwords do not match', 'on'=>'crud_create'],
            ['phone, comment, is_wholesale', 'safe'],
            
            // crud update
            ['name, email', 'required', 'on'=>'crud_update'],
            ['name, lastname', '\common\components\validators\NameValidator', 'on'=>'crud_update'],
            ['comment', 'safe', 'on'=>'crud_update'],
            ['phone, is_wholesale', 'safe', 'on'=>'crud_update'],
            
            // crud filter
            ['id, name, pubilshed, is_wholesale, phone', 'safe', 'on'=>'crud_filter'],
            
            // auth
            ['email, password', 'required', 'on'=>'auth'],
            ['remember_me', 'boolean', 'on'=>'auth'],
            
            // restore_password
            ['email', 'required', 'on'=>'restore_password'],
            
            // restore_password_change
            ['password, repassword', 'required', 'on'=>'restore_password_change'],
            ['password', 'length', 'min'=>6, 'max'=>20, 'on'=>'restore_password_change'],
            // ['password', '\common\components\validators\StrongPasswordValidator', 'on'=>'restore_password_change'],
            ['repassword', 'compare', 'compareAttribute'=>'password', 'message'=>'Пароли не совпадают', 'on'=>'restore_password_change'],
            
            
            // registration_with_coupon
            ['coupon_code, coupon_check_code', 'required', 'on'=>'registration_with_coupon'],
            ['coupon_check_code', 'match', 'pattern'=>'/^.+\s*?-\s*?\d{6}$/', 'on'=>'registration_with_coupon'],
            
            // restore_password_change
            ['password, repassword', 'required', 'on'=>'change_password'],
            ['password', 'length', 'min'=>6, 'max'=>20, 'on'=>'change_password'],
            // ['password', '\common\components\validators\StrongPasswordValidator', 'on'=>'change_password'],            
            ['repassword', 'compare', 'compareAttribute'=>'password', 'message'=>'Пароли не совпадают', 'on'=>'change_password'],
            
            // profile_change_password
            ['password, repassword', 'required', 'on'=>'profile_change_password'],
            ['repassword', 'compare', 'compareAttribute'=>'password', 'message'=>'Пароли не совпадают', 'on'=>'profile_change_password'],
            ['password', '\accounts\components\validators\ProfilePasswordValidator', 'on'=>'profile_change_password'],
            ['password', 'length', 'min'=>6, 'max'=>20, 'on'=>'profile_change_password'],
            // ['password', '\common\components\validators\StrongPasswordValidator', 'on'=>'profile_change_password'],
            // ['repassword, lastpassword', 'safe', 'on'=>'profile_change_password'],
            
            // edit_profile
            ['name, lastname, email', 'required', 'on'=>'edit_profile'],
            ['email', 'unique', 'message'=>'Указанный E-Mail уже зарегистрирован на сайте.', 'on'=>'edit_profile'],
            ['name, lastname', '\common\components\validators\NameValidator', 'on'=>'edit_profile'],
            ['phone', 'safe', 'on'=>'edit_profile'],
        ];        
        
        return $rules;
    }
    
    /**
     * 
     * @see \CActiveRecord::relations()
     * @return [][]
     */
    public function relations()
    {
        return [
            'favoriteProducts'=>[\CActiveRecord::HAS_MANY, '\crud\models\ar\accounts\models\AccountFavoriteProduct', 'account_id', 'index'=>'product_id', 'order'=>'`create_time` DESC'],
            'ordersCount'=>[\CActiveRecord::STAT, '\ecommerce\modules\order\models\Order', 'account_id'],
            'orders'=>[\CActiveRecord::HAS_MANY, '\ecommerce\modules\order\models\Order', 'account_id', 'order'=>'`create_time` DESC'],
        ];
    }
    
    /**
     * Подписи атрибутов модели
     * @see \CActiveRecord::attributeLabels()
     * @return string[]
     */
    public function attributeLabels()
    {
        return [
            'repassword'=>'Повторите пароль',
            'lastpassword'=>'Текущий пароль',
            'login'=>'E-Mail',
            'remember_me'=>'Запомнить меня',
            'is_wholesale'=>'Является оптовым покупателем',
        ];
    }
    
    /**
     * Список доступных ролей
     * @return []
     */
    public function roles()
    {
        return [
            Account::ROLE_RETAIL_BUYER=>'Розничный покупатель',
            Account::ROLE_WHOLESALE_BUYER=>'Оптовый покупатель'
        ];
    }
    
    /**
     * Scope: по номеру телефона
     * @param string $phone номер телефона
     * @return \CComponent
     */
    public function byPhone($phone)
    {
        $c=HDb::criteria();
        
        $c->addColumnCondition(['phone'=>preg_replace('/[^0-9]+/', '', $phone)]);
        $this->owner->getDbCriteria()->mergeWith($c);
        
        return $this->owner;
    }
    
    /**
     * Scope: по адресу электронной почты
     * @param string $email адрес электронной почты
     * @return \CComponent
     */
    public function byEmail($email)
    {
        $c=HDb::criteria();
        
        $c->addColumnCondition(['email'=>$email]);
        $this->owner->getDbCriteria()->mergeWith($c);
        
        return $this->owner;
    }
    
    /**
     * Ожидает модерации
     */
    public function isWaitModeration()
    {
        return ((int)$this->owner->moderated !== 1);
    }
    
    /**
     * Получить ФИО
     * @return string
     */
    public function getFullName()
    {
        return trim($this->owner->lastname . ' ' . $this->owner->name);
    }
    
    /**
     * Получить Имя Отчество
     * @return string
     */
    public function getUserName()
    {
        return $this->owner->name;
    }
    
    /**
     * Получить подпись роли
     * @return string|null
     */
    public function getRoleLabel()
    {
        return A::get($this->owner->roles(), $this->owner->role);
    }
    
    public function getBankInformation()
    {
        $info=[];
        
        $data=$this->owner->bankInfoBehavior->get(true);
        foreach($data as $item) {
            if(!empty($item['title']) && !empty($item['value'])) {
                $info[]=$item;
            }
        }
        
        return $info;
    }
    
    /**
     * Проверить пароль
     * @param string $password пароль для проверки
     * @return boolean
     */
    public function validatePassword($password)
    {
        return \CPasswordHelper::verifyPassword($password, $this->owner->password);
    }
    
    /**
     * Захэшировать пароль
     * @return string
     */
    public function hashPassword()
    {
        return \CPasswordHelper::hashPassword($this->owner->password);
    }
    
    /**
     * Аккаунт активирован?
     * @return bool
     */
    public function isActivated()
    {
        return ((int)$this->owner->published > 0);
    }
    
    /**
     * Активация аккаунта
     * @param string $code код активации для проверки
     * @return bool
     */
    public function activate($code)
    {
        if(!empty($code) && ($this->owner->confirm_code === $code)) {
            $this->owner->published=1;
            $this->owner->last_confirm_code=$this->owner->confirm_code;
            $this->owner->confirm_code='';
        
            return $this->owner->update(['published', 'confirm_code', 'last_confirm_code']);
        }
        
        return false;
    }
    
    /**
     * Авторизация
     * @param string $email email
     * @param string $password пароль
     * @return \crud\models\ar\accounts\models\Account|false
     * В случае успеха, возвращает объект модели определенного пользователя.
     */
    public function auth($email, $password) {
        if(!empty($email) && !empty($password)) {
            if($account=$this->owner->published()->byEmail($email)->find()) {
                if(!$account->isWaitModeration() && $account->validatePassword($password)) {
                    return $account;
                }
            }
        }
        
        return false;
    }
    
    public function getAccountByIdentity($identity)
    {
        if($identity->errorCode) {
            if($account=$this->owner->byEmail($identity->username)->find()) {
                if($account->validatePassword($identity->password)) {
                    return $account;
                }
            }
        }
        
        return null;
    }
    
    /**
     * Авторизация по объекту идентификации пользователя \CUserIdentity
     * @param \CUserIdentity &$identity объект идентификации пользователя
     * @param boolean $returnAccountAuth возвращать результат идентификации
     * только авторизации AccountModelBehavior::auth(). 
     * По умолчанию (FALSE) будет возращен глобальный результат авторизации.
     * @return boolean|null если установлено $returnAccountAuth=true и пользователь 
     * уже авторизован будет возращено NULL. 
     */
    public function authByUserIdentity(&$identity, $returnAccountAuth=false)
    {
        $resultAuth=!$identity->errorCode;
        if($identity->errorCode) {
            if($user=$this->auth($identity->username, $identity->password)) {
                $identity->setState('user_id', $user->id);
                $identity->setState('role', $user->role);
                $identity->errorCode=0;
                $user->updateLoginTime();
                return true;
            }
            else {
                return false;
            }
        }
        return $returnAccountAuth ? null : $resultAuth;
    }
    
    /**
     * Инициализация для объекта пользователя \CWebUser
     * @param \CWebUser &$webUser объект пользователя
     */
    public function initWebUser(&$webUser)
    {
        if(!$webUser->isGuest && $webUser->hasProperty('role') && !$webUser->role && $webUser->user_id) {
            if($user=$this->findByPk($webUser->user_id)) {
                $webUser->role=$user->role;
            }
        }
    }
    
    /**
     * Event: onBeforeValidate 
     * @return boolean
     */
    public function beforeValidate()
    {
        $this->owner->phone=$this->normalizePhone();
        
        return true;
    }
    
    /**
     * Event: onBeforeSave
     * @return boolean
     */
    public function beforeSave()
    {
        if($this->owner->isNewRecord || in_array($this->owner->getScenario(), ['change_password', 'restore_password_change', 'profile_change_password'])) {
            $this->owner->plain_password=$this->owner->password;
            $this->owner->password=$this->hashPassword();
        }
        
        if($this->owner->isNewRecord) {
            if(HAccount::settings()->isRegConfirmMode()) {
                $this->owner->confirm_code=$this->generateConfirmCode();
                $this->owner->check_code=$this->generateConfirmCode();
                $this->owner->published=0;
            }
            else {
                if($this->owner->is_wholesale && in_array($this->owner->getScenario(), ['registration'])) {
                    $this->owner->moderated=0;
                    $this->owner->published=0;
                    $this->owner->role=Account::ROLE_WHOLESALE_BUYER;
                }
                else {
                    $this->owner->moderated=1;
                    $this->owner->published=1;
                    $this->owner->role=Account::ROLE_RETAIL_BUYER;
                }
            }            
        }
        else {
            if($this->owner->is_wholesale && in_array($this->owner->getScenario(), ['crud_create', 'crud_update'])) {
                $this->owner->role=Account::ROLE_WHOLESALE_BUYER;
            }
            elseif($this->owner->role != Account::ROLE_WHOLESALE_BUYER) {
                $this->owner->role=Account::ROLE_RETAIL_BUYER;
            }
        }
                
        $this->owner->phone=$this->normalizePhone();
        
        return true;
    }
    
    /**
     * Event: onAfterSave
     */
    public function afterSave()
    {
        
    }
    
    /**
     * Обновление даты и времени последней успешной авторизации
     */
    public function updateLoginTime()
    {
        $this->owner->login_time=new \CDbExpression('NOW()');
        $this->owner->update(['login_time']);
    }
    
    /**
     * Генерация кода подтверждения
     * @return string
     */
    public function generateConfirmCode()
    {
        return md5(HHash::u());
    }
    
    /**
     * Перегенерировать проверочные коды
     * @param boolean $update сохранить изменения
     */
    public function regenerateCodes($update=false)
    {
        $this->owner->last_confirm_code=$this->owner->confirm_code;
        $this->owner->confirm_code=$this->generateConfirmCode();
        $this->owner->check_code=$this->generateConfirmCode();
        
        if($update) {
            return $this->owner->update(['check_code', 'confirm_code']);
        }
        
        return true;
    }
    
    /**
     * Очистить проверочный код
     * @param boolean $update сохранить изменения
     */
    public function clearConfirmCode($update=false)
    {
        $this->owner->last_confirm_code=$this->owner->confirm_code;
        $this->owner->confirm_code='';
        
        if($update) {
            $this->owner->update(['confirm_code', 'last_confirm_code']);
        }
    }
    
    /**
     * Очистить дополнительный проверочный код
     * @param boolean $update сохранить изменения
     */
    public function clearCheckCode($update=false)
    {
        $this->owner->check_code='';
        
        if($update) {
            $this->owner->update(['check_code']);
        }
    }
    
    /**
     * Нормализация номера телефона для сохранения
     */
    public function normalizePhone()
    {
        return preg_replace('/[^0-9]+/', '', $this->owner->phone);
    }    
    
    /**
     * Форматированный вывод номера телефона
     * @return string
     */
    public function formatPhone()
    {
        return preg_replace('/^(\d)(\d{3})(\d{3})(\d{2})(\d{2})$/', '+$1 ( $2 ) $3 - $4 - $5', $this->owner->phone);
    }    
}
