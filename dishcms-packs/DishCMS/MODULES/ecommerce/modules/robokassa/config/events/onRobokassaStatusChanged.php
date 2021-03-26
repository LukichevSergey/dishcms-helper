<?php
/**
 * @event onRobokassaStatusChanged
 * Вызывается после изменения статуса платежа
 *
 * @param \common\components\events\Event $event объект события
 *
 * Параметры события:
 * payment: \crud\models\ar\robokassa\models\Payment
 *
 */
use ecommerce\modules\robokassa\components\helpers\HRobokassa;

return function(&$event) {
    if($payment=$event->getParam('payment')) {
        
    }
};