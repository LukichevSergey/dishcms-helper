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
     * Политика конфиденциальности
     * @var boolean
     */
    public $privacy;
    
    /**
     * Запомнить авторизацию
     * @var boolean
     */
    public $remember_me;
    
    /**
     * {@inheritDoc}
     * @see \CBehavior::events()
     */
    public function events()
    {
        return [
            'onBeforeValidate'=>'beforeValidate',
            'onBeforeSave'=>'beforeSave'
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
            ['password', 'length', 'min'=>6],
            ['phone', 'unique', 'message'=>'Номер телефона уже занят.', 'except'=>'auth, restore_password, restore_password_change'],
            ['comment', 'length', 'max'=>255],
            ['phone', 'safe'],            
            
            // registration
            ['name, email, phone, role, password, repassword', 'required', 'on'=>'registration'],
            ['repassword', 'compare', 'compareAttribute'=>'password', 'message'=>'Пароли не совпадают', 'on'=>'registration'],
            ['privacy', 'required', 'message'=>'Вы не подтвердили согласие', 'on'=>'registration'],
            ['privacy', 'boolean', 'on'=>'registration'],
            
            // auth
            ['phone, password', 'required', 'on'=>'auth'],
            ['remember_me', 'boolean', 'on'=>'auth'],
            
            // restore_password
            ['phone', 'required', 'on'=>'restore_password'],
            
            // restore_password_change
            ['password, repassword', 'required', 'on'=>'restore_password_change'],
            ['repassword', 'compare', 'compareAttribute'=>'password', 'message'=>'Пароли не совпадают', 'on'=>'restore_password_change'],
            
            // profile
            ['name, email, phone', 'required', 'on'=>'profile, profile_change_password'],
            ['password', 'safe', 'on'=>'profile'],
            ['password', '\accounts\components\validators\ProfilePasswordValidator', 'on'=>'profile_change_password'],
            ['repassword, lastpassword', 'safe', 'on'=>'profile_change_password'],
        ];        
        
        return $rules;
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
            'login'=>'Номер телефона или E-Mail',
            'remember_me'=>'Запомнить меня на этом компьютере'
        ];
    }
    
    /**
     * Список доступных ролей
     * @return []
     */
    public function roles()
    {
        return [
            Account::ROLE_DEFAULT=>'По умолчанию (зарегистрированный пользователь)',
            Account::ROLE_REGISTERED=>'Зарегистрированный пользователь',
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
     * Получить подпись роли
     * @return string|null
     */
    public function getRoleLabel()
    {
        return A::get($this->owner->roles(), $this->owner->role);
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
     * @param string $phone номер телефона
     * @param string $password пароль
     * @return \crud\models\ar\accounts\models\Account|false
     * В случае успеха, возвращает объект модели определенного пользователя.
     */
    public function auth($phone, $password) {
        if(!empty($phone) && !empty($password)) {
            if($account=$this->owner->published()->byPhone($phone)->find()) {
                if($account->validatePassword($password)) {
                    return $account;
                }
            }
        }
        
        return false;
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
            $this->owner->password=$this->hashPassword();
        }
        
        if($this->owner->isNewRecord) {
            if(HAccount::settings()->isRegConfirmMode()) {
                $this->owner->confirm_code=$this->generateConfirmCode();
                $this->owner->check_code=$this->generateConfirmCode();
                $this->owner->published=0;
            }
            else {
                $this->owner->published=1;
            }
        }
        
        $this->owner->phone=$this->normalizePhone();
        
        return true;
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