<?php
/**
 * Конфигурация оплаты заказа
 * @param [] $params дополнительные параметры для формирования параметров оплаты
 * "hash" хэш заказа (\ecommerce\modules\order\models\Order::$hash)
 */
use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use ykassa\components\helpers\HYKassa;
use ecommerce\modules\order\models\Order;

return [
    // событие вызывается после успешного создания платежа
    // перед перенаправлением на форму оплаты
    // обязательный параметр, который будет передан 
    // \YandexCheckout\Model\Payment "payment"
    'on_after_create_payment'=>function($params=[]) {
        if($payment=A::get($params, 'payment')) {
            if($hash=A::get($params, 'hash')) {
                if($order=Order::model()->wcolumns(['hash'=>$hash])->find()) {
                    if($order->yandex_payment_id !== $payment->getId()) {
                        $order->yandex_payment_id=$payment->getId();
                        $order->update(['yandex_payment_id']);
                    }
                }
            }
        }
    },

    // получение параметров для создания платежа
    'payment'=>function($params=[]) {
        if($hash=A::get($params, 'hash')) {
            if($order=Order::model()->wcolumns(['hash'=>$hash])->find()) {
                return [
                    'amount' => [
                        'value' => $order->getTotalPrice(),
                        'currency' => 'RUB'
                    ],
                    'description'=>mb_substr("Оплата заказа #{$order->hash}", 0, 127),
                    'metadata'=>[
                        'order_number'=>$order->id,
                        'order_hash'=>$order->hash
                    ],
                    'confirmation'=>[
                        'type' => 'redirect',
                        'return_url' => Y::createAbsoluteUrl('/')
                    ]
                ];
            }
        }
    },

    // получение параметров для формирования чека
    'receipt'=>function($params=[]) {
        if($hash=A::get($params, 'hash')) {
            if($order=Order::model()->wcolumns(['hash'=>$hash])->find()) {
                $customer=$order->getCustomerData();
                $paymentParams=[
                    'customer' => [
                        'full_name' => A::rget($customer, 'name.value'),
                    ]
                ];
    
                if(HYKassa::getTaxSystem()) {
                    $paymentParams['tax_system_code']=HYKassa::getTaxSystem();
                }
                
                if($phone=HYKassa::getE164Phone(A::rget($customer, 'phone.value'))) {
                    $paymentParams['customer']['phone']=$phone;
                    $paymentParams['phone']=$phone;
                }
    
                if($email=A::rget($customer, 'email.value')) {
                    $paymentParams['customer']['email']=$email;
                    $paymentParams['email']=$email;
                }
    
                $items=[];
                foreach($order->getOrderData() as $item) {
                    $items[]=[
                        'description' => mb_substr(trim(A::rget($item, 'offer.value', '') . ' ' . A::rget($item, 'title.value', 'товар')), 0, 127),
                        'quantity' => (int)A::rget($item, 'count.value', 1),
                        'amount' => [
                            'value' => (int)A::rget($item, 'price.value', 0),
                            'currency' => 'RUB'
                        ],
                        'vat_code' => HYKassa::getTax(),
                        'payment_mode' => HYKassa::getPaymentMethodType(),
                        'payment_subject' => HYKassa::getPaymentSubjectType()
                    ];
                }
    
                $paymentParams['items']=$items;
    
                return $paymentParams;
            }
        }
    },

    // формирование параметров для добавлении записи в историю платежей
    'history'=>function($params=[]) {
        if($hash=A::get($params, 'hash')) {
            if($order=Order::model()->wcolumns(['hash'=>$hash])->find()) {
                return [
                    'int_param_1' => $order->id, // идентификатор заказа                
                ];
            }
        }
    },

    // вызывается при смене статуса платежа
    // @param [] $params дополнительные параметры
    // "status" статус платежа
    'on_change_status'=>function($params=[])
    {
        if($id=A::get($params, 'int_param_1')) {
            if($status=A::get($params, 'status')) {
                if($order=Order::model()->findByPk($id)) {
                    $updateAttributes=[];
                    switch($status) {
                        case HYKassa::API_STATUS_SUCCEEDED:
                            if(!$order->paid) {
                                $order->paid=1;
                                $updateAttributes[]='paid';
                            }
                            if($order->in_paid) {
                                $order->in_paid=0;
                                $updateAttributes[]='in_paid';
                            }
                            break;

                        case HYKassa::API_STATUS_CANCELED:
                            if($order->paid) {
                                $order->paid=0;
                                $updateAttributes[]='paid';
                            }
                            if($order->in_paid) {
                                $order->in_paid=0;
                                $updateAttributes[]='in_paid';
                            }
                            break;
                        
                        case HYKassa::API_STATUS_PENDING:
                        case HYKassa::API_STATUS_WAITING_FOR_CAPTURE:
                        default: 
                            if($order->paid) {
                                $order->paid=0;
                                $updateAttributes[]='paid';
                            }
                            if(!$order->in_paid) {
                                $order->in_paid=1;
                                $updateAttributes[]='in_paid';
                            }
                            break;
                    }     

                    if(!empty($updateAttributes)) {
                        $order->update($updateAttributes);
                    }
                }
            }
        }
    },

    // получение содержимого колонки дополнительной информации для записи истории платежа
    'crud_history_get_info'=>function($params=[]) {
        if($data=A::get($params, 'data')) {
            if($data->int_param_1) {
                if($order=Order::model()->findByPk($data->int_param_1)) {
                    $customer=$order->getCustomerData();                    
                    return [
                    	'Заказ'=>"№{$order->id} от " . Y::formatDate($order->create_time, 'dd.MM.yyyy'),
                        'Системный идентификатор заказа'=>$order->hash,
                        'Покупатель'=>trim(A::rget($customer, 'name.value') . ', ' . A::rget($customer, 'phone.value') . ', ' . A::rget($customer, 'email.value'), ', ')
                    ];
                }
            }
        }
    }
];