<?php
/**
 * @event onAccountLogout
 * Завершение сессии
 *
 * @param \common\components\events\Event $event объект события
 *
 * Возвращаемые параметры:
 * success: boolean
 */

return function(&$event) {
    $event->setParam('success', (\Yii::app()->user && \Yii::app()->user->logout()));
};