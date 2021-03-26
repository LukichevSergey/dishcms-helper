<?php
namespace accounts\controllers;

use common\components\helpers\HArray as A;
use common\components\helpers\HRequest as R;
use common\components\helpers\HModel;
use crud\models\ar\accounts\models\Account;
use accounts\components\helpers\HAccount;
use common\components\helpers\HEvent;

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
            ['allow', 'users'=>['*'], 'actions'=>['index']],
            ['allow', 'users'=>['*'], 'actions'=>['restore', 'restoreConfirm', 'restoreChange', 'restoreRegConfirm']],
            ['allow', 'users'=>['@'], 'roles'=>[Account::ROLE_REGISTERED], 'actions'=>['logout']],
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
            $this->redirect('/accounts/account/index');
        }
        
        $account=HModel::massiveAssignment('\crud\models\ar\accounts\models\Account', true, true, 'auth');
        
        if(HModel::isFormRequest($account, true)) {
            if(HAccount::login($account)) {
                $this->redirect('/accounts/account/index');
            }
            else {
                $account->password='';
                $account->addError('phone', 'Неверный номер телефона или пароль');
            }            
        }        
        
        $this->pageTitle=$this->t('action.index.title');
        
        $this->addHomeBreadcrumb(true);
        $this->addPageBreadcrumb();
        
        $this->render('index', compact('account'));
    }

    /**
     * Action: Выход из личного кабинета
     * 
     */
    public function actionLogout()
    {
        if(HAccount::logout()) {
            $this->redirect('/accounts/auth/index');
        }
        
        $this->redirect('/accounts/account/index');
    }
    
    /**
     * Action: Восстановление пароля
     * 
     */
    public function actionRestore()
    {
        $account=HModel::massiveAssignment('\crud\models\ar\accounts\models\Account', true, true, 'restore_password');
        
        if(HModel::isFormRequest($account, true)) {
            if($account->validate()) {
                if($acc=$account->byPhone($account->phone)->find()) {
                    if(!$acc->published) {
                        if($acc->confirm_code) {
                            // аккаунт не активирован, предложение повторно выслать письмо со ссылкой активации аккаунта
                            if($acc->regenerateCodes(true)) {
                                $this->redirect(['/accounts/auth/restoreRegConfirm', 'c'=>HAccount::getCryptCode($acc)]);
                            }
                            else {
                                $account->addError('phone', 'Доступ не активирован.');
                            }
                        }
                        else {
                            // аккаунт заблокирован
                            $account->addError('phone', 'Доступ закрыт адиминстратором сайта');
                        }
                    }
                    elseif(!$acc->email) {
                        $account->addError('phone', 'В личных данных не был указан почтовый адрес для восстановления');
                    }
                    else {
                        // отправка ссылки восстановления пароля
                        if($acc->regenerateCodes(true)) {
                            $this->redirect(['/accounts/auth/restoreConfirm', 'c'=>HAccount::getCryptCode($acc)]);
                        }
                        else {
                            $account->addError('phone', 'Не удалось отправить письмо. Повторите попытку позднее.');
                        }
                    }
                }
                else {
                    // аккаунт не найден
                    $account->addError('phone', 'Номер телефона не найден');
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
        
        $event=HEvent::raise('onAccountAuthRestoreConfirm', [
            'account'=>$account,
            'email.subject'=>$this->t('action.restore.confirm.email.subject', ['{sitename}'=>\Yii::app()->name])
        ]);
        $view=A::get($event->params, 'sended', false) ? 'restore_confirm' : 'restore_confirm_failed';
        
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
                    if(HModel::isFormRequest($account, true)) {
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
    