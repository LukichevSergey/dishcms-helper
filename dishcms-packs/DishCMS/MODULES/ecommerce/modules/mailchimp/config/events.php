<?php
/**
 * Дополнительные события модуля MailChimp
 * 
 * Список событий модуля MailChimp
 * 
 * "OnYkassaHttpAvisoSuccess" - оплата успешно проведена
 * 
 */
use common\components\helpers\HArray as A;
use mailchimp\components\helpers\HMailChimp;

return [
    'OnYkassaHttpAvisoSuccess'=>[
        function($event) {
            if($orderId=(int)A::get($event->params, 'order_id')) {
                HMailChimp::addListMemberByOrderId($orderId, HMailChimp::SUBSCRIBER_STATUS_SUBSCRIBED);
            }
        }
    ]
];
