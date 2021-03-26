<?php
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
        $event->setParam('success', \Yii::app()->user->login($identity, $event->getParam('duration', 0)));
    }
};