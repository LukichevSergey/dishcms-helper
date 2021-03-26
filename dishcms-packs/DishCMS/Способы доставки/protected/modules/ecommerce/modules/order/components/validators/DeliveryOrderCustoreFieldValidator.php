<?php
namespace ecommerce\modules\order\components\validators;

class DeliveryOrderCustoreFieldValidator extends \CValidator
{
    public function validateAttribute($model, $attribute) {
        if($model->isPickUp()) {
            if(!trim($model->delivery_pickup_point_id)) {
                $this->addError($model, 'delivery_pickup_point_id', 'Необходимо выбрать пункт самовывоза');
            }
        }
        else {
            foreach(['delivery_address_city', 'delivery_address_street', 'delivery_address_house', 'delivery_address_flat'] as $attr) {
                if(!trim($model->$attr)) {
                    $this->addError($model, $attr, 'Обязательно для заполнения');
                }
            }
        }
    }
}