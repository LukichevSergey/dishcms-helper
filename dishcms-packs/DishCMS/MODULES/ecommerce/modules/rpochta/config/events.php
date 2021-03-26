<?php
/**
 * Дополнительные события модуля Почта.России
 */
use common\components\helpers\HArray as A;
use rpochta\components\RPochtaApi;

return [
    'OnYkassaHttpAvisoSuccess'=>[
        function($event) {
            if($orderId=A::get($event->params, 'order_id')) {
                // отправка в Почта.России
                RPochtaApi::i()->newOrder($order->id, true);
            }
        }
    ]
];
