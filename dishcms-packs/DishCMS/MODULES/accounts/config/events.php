<?php
/**
 * События модуля "Аккаунты"
 * 
 */
use common\components\helpers\HArray as A;
use common\ext\email\components\helpers\HEmail;
use common\components\helpers\HEvent;

return [
    // вызывается после успешной регистрации пользователя
    'onAccountRegSuccessed'=>function($event) {
        /** @var \crud\models\ar\accounts\models\Account $account */
        if($account=A::get($event->params, 'account')) {
            return true;
        }
        return false;
    },
    
    // вызывается после регистрации, при необходимости активации аккаунта
    'onAccountRegConfirm'=>function(&$event) {
        $event->params['sended']=false;
        
        /** @var \crud\models\ar\accounts\models\Account $account */
        if($account=A::get($event->params, 'account')) {
            if(!$account->hasErrors() && $account->email) {
                $emailEvent=HEvent::raise('onAccountRegConfirmEmailTemplate');
                $event->params['sended']=HEmail::cmsSend(
                    $account->email, 
                    A::get($event->params, 'email.subject', 'Подтверждение регистрации'), 
                    compact('account'), 
                    A::get($emailEvent->params, 'view', 'accounts.views._email.reg_confirm')
                );
            }
        }
    },
    
    // получение шаблона почтового уведомеления активации аккаунта
    'onAccountRegConfirmEmailTemplate'=>function(&$event) {
        $event->params['view']='accounts.views._email.reg_confirm';
    },
    
    // получение объекта идентфикации пользователя \CUserIdentity
    'onAccountGetUserIdentity'=>function(&$event) {
        $event->params['identity']=null;
        
        /** @var \crud\models\ar\accounts\models\Account $account */
        if($account=A::get($event->params, 'account')) {
            $event->params['identity']=new \UserAuth($account->phone, $account->password);
        }
    },
    
    // вызвается после успешной авторизации, через UserIdentity
    'onAccountAuthIdentitySuccessed'=>function(&$event) {
        $event->params['success']=false;
    
        /** @var \CUserIdentity $identity */
        if($identity=A::get($event->params, 'identity')) {
            $event->params['success']=\Yii::app()->user->login($identity, A::get($event->params, 'duration', 0));
        }
    },
    
    // событие вызывается для проверки активной авторизованности пользователя
    'onAccountIsAuth'=>function(&$event) {
        $event->params['success']=(\Yii::app()->user && !\Yii::app()->user->isGuest);
    },
    
    // событие вызвается для выхода пользователя из личного кабинета
    'onAccountLogout'=>function(&$event) {
        $event->params['success']=(\Yii::app()->user && \Yii::app()->user->logout());
    },
    
    // вызывается при восстановлении пароля
    'onAccountAuthRestoreConfirm'=>function(&$event) {
        $event->params['sended']=false;
        
        /** @var \crud\models\ar\accounts\models\Account $account */
        if($account=A::get($event->params, 'account')) {
            if(!$account->hasErrors() && $account->email) {
                $emailEvent=HEvent::raise('onAccountAuthRestoreConfirmEmailTemplate');
                $event->params['sended']=HEmail::cmsSend(
                    $account->email,
                    A::get($event->params, 'email.subject', 'Восстановление пароля'),
                    compact('account'),
                    A::get($emailEvent->params, 'view', 'accounts.views._email.auth_restore_confirm')
                );
            }
        }
    },
    
    // получение шаблона почтового уведомеления восстановления пароля
    'onAccountAuthRestoreConfirmEmailTemplate'=>function(&$event) {
        $event->params['view']='accounts.views._email.auth_restore_confirm';
    },
    
    // получение активного авторизованного пользователя
    'onAccountGetActiveWebUser'=>function(&$event) {
        $event->params['webUser']=null;
        if(\Yii::app()->user) {
            $event->params['webUser']=\Yii::app()->user;
        }
    }
];