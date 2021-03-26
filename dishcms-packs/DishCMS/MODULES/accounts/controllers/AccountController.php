<?php
namespace accounts\controllers;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HRequest as R;
use common\components\helpers\HModel;
use common\components\helpers\HEvent;
use accounts\components\helpers\HAccount;
use crud\models\ar\accounts\models\Account;

/**
 * Личный кабинет
 *
 */
class AccountController extends \accounts\components\base\BaseController
{
    /**
     * {@inheritDoc}
     * @see \CController::filters()
     */
    public function filters()
    {
        return [
            'accessControl'
        ];
    }
    
    /**
     * {@inheritDoc}
     * @see \CController::accessRules()
     */
    public function accessRules()
    {
        return [
            ['allow', 'users'=>['@'], 'roles'=>[Account::ROLE_REGISTERED]],
            ['deny', 'users'=>['*']]
        ];
    }
    
    /**
     * (non-PHPdoc)
     * @see \CController::behaviors()
     */
    public function behaviors()
    {
        return A::m(parent::behaviors(), [
            
        ]);
    } 
    
    /**
     * Action: Личный кабинет
     */
    public function actionIndex()
    {
        $profile=HAccount::account();
        $profile->setScenario('profile');
        
        if(empty($profile)) {
            HAccount::logout();
            $this->redirect('/accounts/auth/index');
        }
        
        $clearPasswords=true;
        if(HModel::isFormRequest($profile)) {
            $profile=HModel::massiveAssignment($profile);
            
            if($profile->password) {
                $profile->setScenario('profile_change_password');
                $profile=HModel::massiveAssignment($profile);
            }
            
            if($profile->validate()) {
                $attributes=['name', 'email', 'phone'];
                if($profile->isScenario('profile_change_password')) {
                    $attributes[]='password';
                }
                
                if($profile->update($attributes)) {
                    $message='Изменения успешно сохранены.';
                    if($profile->isScenario('profile_change_password')) {
                        $message.=' Установлен новый пароль.';
                    }
                    Y::setFlash(HAccount::FLASH_SUCCESS, $message);
                }
                else {
                    Y::setFlash(HAccount::FLASH_FAIL, 'Произошла ошибка при сохранении профиля. Данные не были изменены.');
                    
                }
                $this->redirect('/accounts/account/index');
            }
            elseif($profile->isScenario('profile_change_password')) {
                $clearPasswords=false;
            }
        }
        
        if($clearPasswords) {
            $profile->password='';
            $profile->repassword='';
            $profile->lastpassword='';
        }
        
        $this->pageTitle=$this->t('action.index.title');
        
        $this->addPageBreadcrumb();
        
        $this->render('index', compact('profile'));
    }    
}