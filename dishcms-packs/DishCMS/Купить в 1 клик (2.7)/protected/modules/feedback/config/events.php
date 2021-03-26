<?php
/**
 * Дополнительные события модуля Обратная связь
 * 
 * Список событий модуля "Обратная связь"
 * 
 * "OnFeedbackNewMessageSuccess" - новое сообщение (параметры: $factory, $model)
 * 
 */
use common\components\helpers\HEvent;
use common\ext\email\components\helpers\HEmail;
use DOrder\models\CustomerForm;
use DOrder\models\DOrder;

return [
    'OnFeedbackNewMessageSuccess'=>[
        function($event) {
            $factory = $event->params['factory'];
            $model = $event->params['model'];
            if ($factory->getId() == 'buy1click' ) {
                if($product = \Product::model()->findByPk($model->product_id)) {
                    $customerForm = new CustomerForm();
                    $customerForm->name = $model->name;
                    $customerForm->phone = $model->phone;
                    $customerForm->privacy_policy = 1;

                    $order = new DOrder();
                    $order->customer_data = $customerForm->getAttributes(null, true, true, true);
                    $order->order_data = serialize([[
                        'id' => ['label'=>'Идентификатор', 'value' => $product->id],
                        'model' => ['label'=>'Модель', 'value' => 'Product'],
                        'title' => ['label'=>'Заголовок', 'value' => $product->title],
                        'price' => ['label'=>'Цена', 'value' => $product->price],
                        'image' => ['label'=>'Изображение', 'value' => $product->mainImageBehavior->getSrc()],
                        'count' => ['label'=>'Количество', 'value' => 1]
                    ]]);
                    $order->comment = 'Купить в 1 клик';

                    if($order->save()) {
                        $model->product_title = $product->title;
                        $model->save();

                        HEvent::raise('OnDOrderNewOrderSuccess', [
                            'order'=>$order,
                            'clientEmail'=>$customerForm->getEmailForNotification()
                        ]);
                    }
                }
                else {
                    $model->product_title = 'Товар не найден';
                    HEmail::cmsAdminSend(true, [
                        'factory'=>$factory,
                        'model'=>$model,
                    ], 'feedback.views._email.new_message_success');
                }
            }
            else {
                HEmail::cmsAdminSend(true, [
                    'factory'=>$factory,
                    'model'=>$model,
                ], 'feedback.views._email.new_message_success');
            }
        }
    ]
];
