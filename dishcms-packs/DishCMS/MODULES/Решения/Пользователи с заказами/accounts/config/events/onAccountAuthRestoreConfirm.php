<?php
/**
 * @event onAccountActivatedSuccessed
 * Вызывается при отправки письма на восстановление пароля
 *
 * @param \common\components\events\Event $event объект события
 *
 * Параметры события:
 * account: \crud\models\ar\accounts\models\Account
 *
 */
use accounts\components\helpers\HAccount;
use accounts\components\helpers\HAccountEmail;

return function(&$event) {
    if($account=$event->getParam('account')) {
        if($account->email) {
            $event->setParam('sended', HAccountEmail::sendMail($account->email, 'restore_password', compact('account')));
        }
    }
};