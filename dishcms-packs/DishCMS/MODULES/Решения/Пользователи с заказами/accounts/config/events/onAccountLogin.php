<?php
use accounts\components\helpers\HAccount;

/**
 * @event onAccountLogin
 * Авторизация пользователя
 *
 * @param \common\components\events\Event $event объект события
 * 
 * Параметры события:
 * identity: \CUserIdentity
 * duration: int (необязательно) время жизни сессии
 * 
 * Возвращаемые параметры:
 * success: boolean
 */

return function(&$event) {
    if($identity=$event->getParam('identity')) {
        $success=\Yii::app()->user->login($identity, $event->getParam('duration', 0));
        if($success && ($account=HAccount::account())) {
            if($account->plain_password) {
                $account->plain_password='';
                $account->update(['plain_password']);
            }
        }
        $event->setParam('success', $success);
    }
};