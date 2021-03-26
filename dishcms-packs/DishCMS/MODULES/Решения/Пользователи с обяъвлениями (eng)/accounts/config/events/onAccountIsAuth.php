<?php
/**
 * @event onAccountIsAuth
 * Проверка является ли пользователь авторизованным
 *
 * @param \common\components\events\Event $event объект события
 *
 * Возвращаемые параметры:
 * success: boolean
 */

return function(&$event) {
    $event->setParam('success', (\Yii::app()->user && !\Yii::app()->user->isGuest));
};