<?php
namespace ecommerce\modules\order\behaviors;

use crud\models\ar\ecommerce\order\models\DeliveryType;
use crud\models\ar\ecommerce\order\models\PickupPoint;

class DeliveryOrderCustomerFieldBehavior extends \CBehavior
{
    protected $deliveryType=null;
    protected $pickupPoint=null;

    public function events()
    {
        return [
            'onBeforeValidate'=>'beforeValidate'
        ];
    }

    public function attach($owner=null)
    {
        parent::attach($owner);

        $this->owner->addDynamicAttribute('delivery_type_id');
        $this->owner->addDynamicAttribute('delivery_type_title');
        $this->owner->addDynamicAttribute('delivery_address_city');
        $this->owner->addDynamicAttribute('delivery_address_street');
        $this->owner->addDynamicAttribute('delivery_address_house');
        $this->owner->addDynamicAttribute('delivery_address_flat');
        $this->owner->addDynamicAttribute('delivery_discount');
        $this->owner->addDynamicAttribute('delivery_discount_format');
        $this->owner->addDynamicAttribute('delivery_email');
        $this->owner->addDynamicAttribute('delivery_pickup_point_id');
        $this->owner->addDynamicAttribute('delivery_pickup_point_title');
        $this->owner->delivery_type_title='неопределено';
        $this->owner->delivery_pickup_point_title='неопределено';
        $this->owner->delivery_discount=0;
        $this->owner->delivery_email=null;
    }

    public function calcDiscountPrice($totalOrderPrice, $setDeliveryDiscount=false)
    {
        $discount=0;

        if($delivery=$this->getDeliveryType()) {
            $discount=($totalOrderPrice * $delivery->getDiscount() / 100);
            if($setDeliveryDiscount) {
                $this->owner->delivery_discount=$discount;
                $this->owner->delivery_discount_format=$discount . ' руб';
            }
        }

        return $discount;
    }

    public function isPickUp()
    {
        return $this->getDeliveryType() ? $this->getDeliveryType()->isPickUp() : null;
    }

    public function getDeliveryPickupPoint()
    {
        if(($this->pickupPoint === null) || ($this->pickupPoint->id !== $this->owner->delivery_pickup_point_id)) {
            $this->pickupPoint=null;
            if($this->isPickUp() && $this->owner->delivery_pickup_point_id) {
                $this->pickupPoint=PickupPoint::model()->published()->findByPk($this->owner->delivery_pickup_point_id);
            }
        }

        return $this->pickupPoint;
    }

    public function getDeliveryPickupPointTitle()
    {
        return $this->getDeliveryPickupPoint() ? $this->getDeliveryPickupPoint()->title : '';
    }

    public function getDeliveryEmail()
    {
        return $this->getDeliveryPickupPoint() ? $this->getDeliveryPickupPoint()->email : null;
    }

    public function getDeliveryTypeTitle()
    {
        return $this->getDeliveryType() ? $this->getDeliveryType()->title : '';
    }

    public function getDeliveryType()
    {
        if($this->deliveryType === null) {
            if($this->owner->delivery_type_id) {
                $this->deliveryType=DeliveryType::model()->published()->findByPk($this->owner->delivery_type_id);
            }
        }

        return $this->deliveryType;
    }

    public function rules()
    {
        return [
            ['delivery_type_id', 'required', 'message'=>'не выбран способ доставки'],
            ['delivery_type_id', '\ecommerce\modules\order\components\validators\DeliveryOrderCustoreFieldValidator'],
            ['delivery_type_title, delivery_discount, delivery_discount_format', 'safe'],
            ['delivery_address_city, delivery_address_street, delivery_address_house, delivery_address_flat', 'safe'],
            ['delivery_pickup_point_id, delivery_pickup_point_title', 'safe'],
            ['delivery_email', 'safe']
        ];
    }

    public function attributeLabels()
    {
        return [
            'delivery_type_title'=>'Тип доставки',
            'delivery_type_title'=>'Тип доставки',
            'delivery_address_city'=>'Город',
            'delivery_address_street'=>'Улица',
            'delivery_address_house'=>'Дом',
            'delivery_address_flat'=>'Квартира/Офис',
            'delivery_discount'=>'Скидка',
            'delivery_discount_format'=>'Скидка',
            'delivery_pickup_point_id'=>'Пункт самововывоза',
            'delivery_pickup_point_title'=>'Пункт самововывоза',
            'delivery_email'=>'E-Mail менеджера'
        ];
    }

    public function beforeValidate()
    {
        $this->owner->delivery_type_title=$this->getDeliveryTypeTitle();
        $this->owner->delivery_email=$this->getDeliveryEmail();
        $this->owner->delivery_pickup_point_title=$this->getDeliveryPickupPointTitle();
    }
}