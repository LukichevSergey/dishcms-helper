<?php
/**
 * @event onAccountGetActiveWebUser
 * Получить активный объект \CWebUser
 *
 * @param \common\components\events\Event $event объект события
 *
 * Возвращаемые параметры:
 * webUser: \CWebUser
 */

return function(&$event) {
    $event->setParam('webUser', \Yii::app()->user);
};