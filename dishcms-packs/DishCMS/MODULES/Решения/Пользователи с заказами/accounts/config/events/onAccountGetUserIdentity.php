<?php
/**
 * @event onAccountGetUserIdentity
 * Получение объекта идентфикации пользователя \CUserIdentity
 *
 * @param \common\components\events\Event $event объект события
 * 
 * Параметры события:
 * account: \crud\models\ar\accounts\models\Account
 * 
 * Возвращаемые параметры:
 * identity: \CUserIdentity (в случае успеха)
 */

return function(&$event) {
    if($account=$event->getParam('account')) {
        $event->setParam('identity', new \UserAuth($account->email, $account->password));
    }
};