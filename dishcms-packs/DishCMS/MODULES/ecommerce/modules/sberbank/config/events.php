<?php
use common\components\helpers\HArray as A;
use ecommerce\modules\order\models\Order;

return [
    /**
     * Event: запрос регистрации заказа прошел успешно
     * @param string id идентификатор платежа
     * @param [] result данные ответа
     */
    'onSberbankGetOrderStatusSuccess'=>function(&$event) {
        if($paymentId=A::get($event->params, 'id')) {
            /** @var \ecommerce\modules\order\models\Order $order */
            if($order=Order::model()->findByAttributes(['payment_id'=>$paymentId])) {
                $result=A::get($event->params, 'result');
                if((int)$result['OrderStatus'] === 2) { // Проведена полная авторизация суммы заказа
                    if((int)$result['Amount'] === (int)$order->getFullPrice() * 100) {
                        $order->paid=1;
                        $order->update(['paid']);
                    }
                }
            }
        }
    },
    
    /**
     * Event: запрос регистрации заказа прошел неуспешно
     * @param string id идентификатор платежа
     * @param [] result данные ответа
     */
    'onSberbankGetOrderStatusFail'=>function(&$event) {
    
    },
    
    /**
     * Event: запрос регистрации заказа прошел успешно
     * @param string id идентификатор заказа
     * @param [] result данные ответа
     */
    'onSberbankRegisterSuccess'=>function(&$event) {
        if($paymentId=A::get($event->params, 'id')) {
            /** @var \ecommerce\modules\order\models\Order $order */
            if($order=Order::model()->findByAttributes(['hash'=>$paymentId])) {
                $result=A::get($event->params, 'result');
                $order->payment_id=$result['orderId'];
                $order->update(['payment_id']);
            }
        }
    },
    
    /**
     * Event: запрос регистрации заказа прошел неуспешно
     * @param string id идентификатор заказа
     * @param [] result данные ответа
     */
    'onSberbankRegisterFail'=>function(&$event) {
        
    },
    
    /**
     * Event: получение номера заказа
     * @param string id идентификатор платежа
     * @return string orderNumber номера заказа
     */
    'onSberbankRegisterGetOrderNumber'=>function(&$event) {
        $event->params['orderNumber']=null;
        if($paymentId=A::get($event->params, 'id')) {
            /** @var \ecommerce\modules\order\models\Order $order */
            if($order=Order::model()->findByAttributes(['hash'=>$paymentId])) {
                $event->params['orderNumber']='#' . $order->id . '-' . $_SERVER['HTTP_HOST'];
            }
        }
    },
    
    /**
     * Event: получение суммы заказа
     * @param string id идентификатор заказа
     * @return integer amount сумма в копейках
     */
    'onSberbankRegisterGetAmount'=>function(&$event) {
        $event->params['amount']=null;
        if($paymentId=A::get($event->params, 'id')) {
            /** @var \ecommerce\modules\order\models\Order $order */
            if($order=Order::model()->findByAttributes(['hash'=>$paymentId])) {
                $event->params['amount']=$order->getFullPrice() * 100;
            }
        }
    },
    
    /**
     * Event: получение дополнительных параметров для запроса регистрации заказа
     * @param string id идентификатор заказа
     * @return [] params данные для параметра "params"
     */
    'onSberbankRegisterGetParams'=>function(&$event) {
        $event->params['params']=[];
        if($paymentId=A::get($event->params, 'id')) {
            /** @var \ecommerce\modules\order\models\Order $order */
            if($order=Order::model()->findByAttributes(['hash'=>$paymentId])) {
                if($customer=$order->getCustomerData()) {
                    if(!empty($customer['email']['value'])) {
                        $event->params['params'][]=[
                            'name'=>'email',
                            'value'=>$customer['email']['value']
                        ];
                    }
                }
            }
        }
    },
    
    /**
     * Event: Получение списка товаров заказа
     * @param string id идентификатор заказа
     * @return [] orderBundle состав заказа 
     */
    'onSberbankRegisterGetOrderBundle'=>function(&$event) {
        $event->params['orderBundle']=null;
        if($paymentId=A::get($event->params, 'id')) {
            /** @var \ecommerce\modules\order\models\Order $order */
            if($order=Order::model()->findByAttributes(['hash'=>$paymentId])) {
                $orderBundle=[];
                $orderBundle['cartItems']=[];                
                $orderBundle['cartItems']['items']=[];
                
                foreach ($order->getOrderData() as $item) {
                    $item['price']['value'] = (int)$item['price']['value'] * 100;
					$title=trim($item['title']['value']);
                    if(mb_strlen($title) > 100) {
                    	$title=mb_substr($title, 0, 97) . '...';
                    }
                        
                    $orderBundle['cartItems']['items'][] = [
                        'positionId'=>'product_' . $item['id']['value'],
                        'name'=>$title,
                        'quantity'=>[
                            'value'=>(int)$item['count']['value'],
                            'measure'=>'шт.'
                        ],
                        'itemAmount'=>$item['price']['value'] * $item['count']['value'],                        
                        'itemCode'=>'product_' . $item['id']['value'],                        
                        'itemPrice'=>$item['price']['value'],                        
                        'tax'=>[                            
                            'taxType' => 0, // без НДС                            
                        ],                        
                    ];
                }
                
                
                if($deliveryPrice=$order->getDeliveryPrice($order->getTotalPrice())) {
                    $orderBundle['cartItems']['items'][]=[
                        'positionId'=>'delivery',
                        'name'=>'Доставка',
                        'quantity'=>['value'=>1, 'measure'=>'шт.'],
                        'itemAmount'=>($deliveryPrice * 100),
                        'itemCode'=>'delivery',
                        'itemPrice'=>($deliveryPrice * 100),
                        'tax'=>[
                            'taxType'=>0, // без НДС
                        ],
                    ];
                }
                
                $event->params['orderBundle']=$orderBundle;
            }
        }
    }
];
