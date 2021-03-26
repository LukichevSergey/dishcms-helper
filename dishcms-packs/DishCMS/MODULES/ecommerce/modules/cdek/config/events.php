<?php
/**
 * Дополнительные события модуля СДЭК
 */
use common\components\helpers\HArray as A;
use cdek\components\CdekApi;

return [
    'OnYkassaHttpAvisoSuccess'=>[
        function($event) {
            if($orderId=A::get($event->params, 'order_id')) {
                // отправка в СДЭК
                CdekApi::i()->newOrder($orderId, true);
            }
        }
    ]
];
