<?php
/**
 * События модуля "Аккаунты"
 * 
 */
use common\components\helpers\HArray as A;
use common\ext\email\components\helpers\HEmail;
use common\components\helpers\HEvent;
use accounts\components\helpers\HAccount;

return [
    // вызывается после регистрации, при необходимости активации аккаунта
    'onAccountRegConfirm'=>function(&$event) {
        $event->setParam('sended', false);
        /*
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
        */
    },

];