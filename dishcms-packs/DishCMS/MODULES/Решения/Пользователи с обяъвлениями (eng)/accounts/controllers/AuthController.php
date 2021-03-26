<?php
namespace accounts\controllers;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HRequest as R;
use common\components\helpers\HModel;
use crud\models\ar\accounts\models\Account;
use accounts\components\helpers\HAccount;
use common\components\helpers\HEvent;
use common\components\helpers\HAjax;

/**
 * Контроллер авторизации
 *
 */
class AuthController extends \accounts\components\base\BaseController
{
    /**
     * {@inheritDoc}
     * @see \CController::filters()
     */
    public function filters()
    {
        return [
            'accessControl',
            'ajaxOnly +ajaxLogin'
        ];
    }
    
    /**
     * {@inheritDoc}
     * @see \CController::accessRules()
     */
    public function accessRules()
    {
        return [
            ['allow', 'users'=>['*'], 'actions'=>['index', 'ajaxLogin']],
            ['allow', 'users'=>['*'], 'actions'=>['restore', 'restoreConfirm', 'restoreChange', 'restoreRegConfirm']],
            ['allow', 'users'=>['@'], 'roles'=>[Account::ROLE_AIRLINE_MRO, Account::ROLE_AIRPORT, Account::ROLE_RESELLER], 'actions'=>['logout']],
            ['deny', 'users'=>['*']]
        ];
    }
    
    /**
     * Action: Авторизация
     *
     */
    public function actionIndex()
    {
        if(HAccount::isAuth()) {
            $this->redirect('/accounts/account');
        }
        
        $this->pageTitle=$this->t('action.index.title');
        
        $this->addHomeBreadcrumb(true);
        $this->addPageBreadcrumb();
        
        $this->render('index', compact('account'));
    }
    
    public function actionAjaxLogin()
    {
        $ajax=HAjax::start();
        
        if(HAccount::isAuth()) {
            $ajax->success=true;
            
            if(\D::role('admin')) {
                $ajax->data['admin']=1;
            }
        }
        else {
            $account=HModel::massiveAssignment('\crud\models\ar\accounts\models\Account', true, true, 'auth');
            if(HModel::isFormRequest($account, true)) {
                if(HAccount::loginByAccount($account)) {
                    $ajax->success=true;
                }
                elseif(HAccount::loginByAdmin($account)) {
                    $ajax->success=true;
                    $ajax->data['admin']=1;
                }
                
                $account->password='';
                if(!$ajax->success && $account->hasErrors()) {
                    $out=[];
                    foreach($account->getErrors() as $attribute=>$errors) {
                        $out[\CHtml::modelName($account).'_'.$attribute]=$errors;
                    }
                    echo json_encode($out, JSON_UNESCAPED_UNICODE);
                    exit;
                }
            }
        }
        
        $ajax->end();
    }

    /**
     * Action: Выход из личного кабинета
     * 
     */
    public function actionLogout()
    {
        if(HAccount::logout()) {
            $this->redirect('/accounts/auth');
        }
        
        $this->redirect('/accounts/account');
    }
    
    /**
     * Action: Восстановление пароля
     * 
     */
    public function actionRestore()
    {
        $account=HModel::massiveAssignment('\crud\models\ar\accounts\models\Account', true, true, 'restore_password');
        
        if($this->isAjaxValidation(R::post('afid'), true)) {
            echo \CActiveForm::validate($account);
            Y::end();
        }
        elseif(HModel::isFormRequest($account, true)) {
            if($account->validate()) {
                if($acc=$account->byEmail($account->email)->find()) {
                    if($acc->isWaitModeration()) {
                        $account->addError('email', 'Your account is awaiting moderation.');
                    }
                    elseif(!$acc->published) {
                        $account->addError('email', 'Your account has been suspended.');
                    }
                    /*
                    if(!$acc->published) {
                        if(false && $acc->confirm_code) {
                            // аккаунт не активирован, предложение повторно выслать письмо со ссылкой активации аккаунта
                            if($acc->regenerateCodes(true)) {
                                $this->redirect(['/accounts/auth/restoreRegConfirm', 'c'=>HAccount::getCryptCode($acc)]);
                            }
                            else {
                                $account->addError('email', 'Доступ не активирован.');
                            }
                        }
                        else {
                            // аккаунт заблокирован
                            $account->addError('email', 'Доступ закрыт адиминстратором сайта');
                        }
                    }
                    elseif(!$acc->email) {
                        $account->addError('email', 'В личных данных не был указан почтовый адрес для восстановления');
                    }
                    */
                    else {
                        // отправка ссылки восстановления пароля
                        if($acc->regenerateCodes(true)) {
                            $this->redirect(['/accounts/auth/restoreConfirm', 'c'=>HAccount::getCryptCode($acc)]);
                        }
                        else {
                            $account->addError('email', 'Failed to send email. Please try again later.');
                        }
                    }
                }
                else {
                    // аккаунт не найден
                    $account->addError('email', 'Your account not found');
                }
            }
        }
        
        $this->pageTitle=$this->t('action.restore.title');
        
        $this->addHomeBreadcrumb();
        $this->addPageBreadcrumb();        
        
        $this->render('restore', compact('account'));
    }
    
    /**
     * Action: Восстановление пароля. Ссылка подтверждения.
     * 
     */
    public function actionRestoreConfirm()
    {        
        $account=HAccount::getAccountByCryptCode(R::get('c'), true);
        $account->clearCheckCode(true);
        
        $event=HEvent::raise('onAccountAuthRestoreConfirm', compact('account'));
        $view=$event->getParam('sended') ? 'restore_confirm' : 'restore_confirm_failed';
        
        $this->pageTitle=$this->t('action.restore.confirm.title');
        
        $this->addHomeBreadcrumb(true);
        $this->addPageBreadcrumb();
        
        $this->render($view, compact('account'));
    }
    
    /**
     * Action: Восстановление пароля. Изменение пароля.
     * @todo в разработке
     */
    public function actionRestoreChange()
    {
        if($code=R::get('code')) {
            $view='restore_change_failed';
            if($account=Account::model()->wcolumns(['confirm_code'=>$code, 'last_confirm_code'=>$code], 'OR')->find()) {
                $account->setScenario('restore_password_change');
                
                if($account->confirm_code == $code) {
                    $view='restore_change';
                    if($this->isAjaxValidation(R::post('afid'), true)) {
                        echo \CActiveForm::validate($account);
                        Y::end();
                    }
                    elseif(HModel::isFormRequest($account, true)) {
                        $account=HModel::massiveAssignment($account);
                        if($account->validate()) {
                            $account->clearConfirmCode();
                            if($account->update(['password', 'confirm_code', 'last_confirm_code'])) {
                                $view='restore_change_changed';
                            }
                        }
                    }
                }
                
                $account->password='';
            }
            
            $this->pageTitle=$this->t('action.restore.change.title');
            
            $this->addHomeBreadcrumb(true);
            $this->addPageBreadcrumb();
            
            $this->render($view, compact('account'));
        }
        else {
            R::e404();
        }
    }
    
    /**
     * Action: Восстановление пароля. Аккаунт не активирован.
     * 
     */
    public function actionRestoreRegConfirm()
    {
        $code=R::get('c');
        
        $account=HAccount::getAccountByCryptCode($code, true);
        
        $this->pageTitle=$this->t('action.restore.confirm.title');
        
        $this->addHomeBreadcrumb(true);
        $this->addPageBreadcrumb();
        
        $this->render('restore_reg_confirm', compact('account', 'code'));
    }
}
    