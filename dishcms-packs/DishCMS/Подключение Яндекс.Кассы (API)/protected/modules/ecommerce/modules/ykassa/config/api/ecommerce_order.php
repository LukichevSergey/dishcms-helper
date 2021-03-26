<?php
/**
 * Формирование данных для чека модели \ecommerce\order\models\Order
 * @param [] $params параметры
 * "id" (integer) идентификатор заказа
 * "order" (\ecommerce\order\models\Order) объект заказа
 * Может быть передан либо "id", либо "order". 
 * Приоритет у параметра "id".
 */
use ykassa\components\helpers\HYKassa;

return [
  // получение параметров для создания платежа
  'payment'=>function($params=[]) {
    // @todo
  },
  
  // получение параметров для формирования чека
  'receipt'=>function($params=[]) {
    if(!empty($params['id'])) {
        $order=\ecommerce\order\models\Order::modelById($params['id']);
    }
    elseif(!empty($params['order'])) {
        $order=$params['order'];
    }

    if(!empty($order) && ($order instanceof \ecommerce\order\models\Order)) {
        $customerData=$order->getCustomerData();
        $orderData=$order->getOrderData();
        // @todo
        /*
        {
            "id": "rt-1da5c87d-0984-50e8-a7f3-8de646dd9ec9",
            "type": "payment",
            "payment_id": "215d8da0-000f-50be-b000-0003308c89be",
            "status": "succeeded",
            "fiscal_document_number": "3986",
            "fiscal_storage_number": "9288000100115785",
            "fiscal_attribute": "2617603921",
            "registered_at": "2019-05-13T17:56:00.000+03:00",
            "fiscal_provider_id": "fd9e9404-eaca-4000-8ec9-dc228ead2345",
            "tax_system_code": HYKassa::settings()->tax_system,
            "items": [
              {
                "description": "Сapybara",
                "quantity": 5,
                "amount": {
                  "value": "2500.50",
                  "currency": "RUB"
                },
                "vat_code": HYKassa::settings()->tax,
                "payment_mode": HYKassa::settings()->payment_method_type,
                "payment_subject": HYKassa::settings()->payment_subject_type
              }
            ]
          }
        */
    }

    return null;
  },

  // формирование параметров для добавлении записи в историю платежей
  'history'=>function($params=[]) {

  },

  // вызывается при смене статуса платежа
  // @param [] $params дополнительные параметры
  // "status" статус платежа
  'on_change_status'=>function($params=[])
  {
    if($status=A::get($params, 'status')) {
            
    }
  },

  // получение содержимого колонки дополнительной информации для записи истории платежа
  'crud_history_get_info'=>function($data) {

  }
];