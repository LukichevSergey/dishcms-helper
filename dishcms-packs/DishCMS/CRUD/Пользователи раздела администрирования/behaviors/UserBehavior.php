<?php
/**
 * Основное поведение CRUD-модели \crud\models\ar\Users
 * 
 */
namespace crud\models\ar\Users\behaviors;

use common\components\helpers\HArray as A;
use common\components\helpers\HDb;

class UserBehavior extends \CBehavior
{
    public $repassword;
    
    public function events()
    {
        return [
            'onBeforeSave'=>'beforeSave'
        ];
    }
    
    public function rules()
    {
        return [
            ['login, role', 'required', 'except'=>'change_password'],
            ['password, repassword, ', 'required', 'on'=>'insert, change_password'],
            ['password', 'length', 'min'=>6, 'on'=>'insert, change_password'],
            ['repassword', 'compare', 'compareAttribute'=>'password', 'on'=>'insert, change_password', 'message'=>'Пароли не совпадают'],            
            ['email', 'unique'],
            ['name, role', 'safe'],
            ['comment', 'length', 'max'=>255],
        ];
    }
    
    public function attributeLabels()
    {
        return [
            'repassword'=>'Повторите пароль'
        ];
    }
    
    public function roles()
    {
        return [
        ];
    }
    
    public function byLogin($login)
    {
        $c=HDb::criteria();
        
        $c->addCondition('LOWER(`login`)=:login');
        $c->params=['login'=>strtolower($login)];
        
        $this->owner->getDbCriteria()->mergeWith($c);
        
        return $this->owner;
    }
    
    public function getRoleLabel()
    {
        return A::get($this->owner->roles(), $this->owner->role);
    }
    
    public function validatePassword($password)
    {
        return \CPasswordHelper::verifyPassword($password, $this->owner->password);
    }
    
    public function auth($login, $password) {
        if($model=$this->owner->byLogin($login)->find()) {
            if($model->validatePassword($password)) {
                return $model;
            }
        }
        return false;
    }
    
    public function authByUserIdentity(&$identity) 
    {
        if($identity->errorCode) {
            if($user=$this->auth($identity->username, $identity->password)) {
                $identity->setState('user_id', $user->id);
                $identity->setState('role', $user->role);
                $identity->errorCode=0;
            }
        }
        return !$identity->errorCode;
    }
    
    public function initWebUser(&$webUser) {
        if(!$webUser->isGuest && !$webUser->role && $webUser->user_id) {
            if($user=$this->findByPk($webUser->user_id)) {
                $webUser->role=$user->role;
	        }
	    }
    }
    
    public function beforeSave()
    {
        if($this->owner->isNewRecord || ($this->owner->getScenario() == 'change_password')) {
            $this->owner->password = \CPasswordHelper::hashPassword($this->owner->password);
        }        
        return true;
    }
}