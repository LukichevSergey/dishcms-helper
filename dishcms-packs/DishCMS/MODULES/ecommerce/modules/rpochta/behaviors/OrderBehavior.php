<?php
/**
 * Поведение для заказа доставки Почта.России
 * 
 */
namespace rpochta\behaviors;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HDb;
use common\components\helpers\HModel;
use rpochta\components\RPochtaApi;
use rpochta\components\RPochtaConst;
use rpochta\components\helpers\HRPochta;
use rpochta\models\Order;

class OrderBehavior extends \CBehavior
{
    public $attributeProductWeight='weight';
    public $attributeProductVolume='volume';
    public $attributeDeliveryType='delivery_type';
    public $deliveryTypeRPochtaValue='rpochta';
    
    /**
     * @var float|false объявленная стоимость товара
     */
    public $packageItemCost=false;
    
    public $rpochta_errors;
    
    public function relations()
    {
        return [
            'rpochta'=>[\CActiveRecord::HAS_ONE, '\rpochta\models\Order', 'order_id']
        ];
    }
    
    public function events()
    {
        return [
            'onBeforeValidate'=>'beforeValidate',
            'onAfterSave'=>'afterSave'
        ];
    }
    
    public function attributeLabels()
    {
        return [
            'rpochta_errors'=>'Почта.России'
        ];
    }
    
    public function isRPochtaDeliveryType()
    {
        $customer=$this->owner->getCustomerData();
        $deliveryType=A::rget($customer, $this->attributeDeliveryType.'.value');
        return ($deliveryType == $this->deliveryTypeRPochtaValue);
    }
    
    public function beforeValidate()
    {
        if(!$this->isRPochtaDeliveryType() || ($this->owner->scenario!='create')) {
            return true;
        }
        
        $this->owner->rpochta=HModel::massiveAssignment('\rpochta\models\Order', true);
        $this->owner->rpochta->setScenario($this->owner->rpochta->getScenarioByMode());        
        
        $valided=$this->owner->rpochta->validate();
        if(!$valided) {
            $this->addErrors($this->owner->rpochta);
        }
        
        return $valided;
    }
    
    public function afterSave()
    {
        $rpochta=$this->owner->rpochta;
        
        if($rpochta->isNewRecord && $this->isRPochtaDeliveryType()) {
            $this->owner->rpochta->order_id=$this->owner->id;
            $this->owner->rpochta->order_number=$this->owner->hash;
            $this->owner->rpochta->create_time=new \CDbExpression('NOW()');
            $this->owner->rpochta->status=$rpochta::STATUS_WAIT;
            
            // корректируем Категорию РПО
            // @FIXME поддержка всего двух категорий РПО
            if((int)HRPochta::settings()->rpochta_insr_value > 0) {
                $this->owner->rpochta->rpo_category=RPochtaConst::RPO_CATEGORY_WITH_DECLARED_VALUE;
            }
            else {
                $this->owner->rpochta->rpo_category=RPochtaConst::RPO_CATEGORY_ORDINARY;
            }
            
            // установка данных получателя
            $customer=$this->owner->getCustomerData();
            $this->owner->rpochta->given_name=trim(A::rget($customer, 'name.value'));
            $this->owner->rpochta->given_midname=trim(A::rget($customer, 'midname.value'));
            $this->owner->rpochta->given_surname=trim(A::rget($customer, 'lastname.value'));
            $this->owner->rpochta->given_phone=HRPochta::normalizePhone(A::rget($customer, 'phone.value'));
            
            // получение дополнительной информации об ОПС
            if($rpochta->isOpsMode()) {
                $opsData=RPochtaApi::i()->opsNearby([
                    'latitude'=>$this->owner->rpochta->ops_latitude,
                    'longitude'=>$this->owner->rpochta->ops_longitude,
                    'top'=>50,
                ]);
                
                if(!empty($opsData)) {
                    foreach($opsData as $ops) {
                        if(($ops['address-source'] == $this->owner->rpochta->ops_address) 
                            && ($ops['postal-code'] == $this->owner->rpochta->ops_index))
                        {
                            $this->owner->rpochta->ops_data=json_encode($ops, JSON_UNESCAPED_UNICODE);
                            break;
                        }
                    }
                }
            }
            else {
                $this->owner->rpochta->address_data=HRPochta::getAddressData($this->owner->rpochta->getFullAddressTo(), true);
            }
            
            $items=[];
            $totalWeight=0;
            $orderItems=$this->owner->getOrderData();
            foreach($orderItems as $item) {
                $weight=A::rget($item, $this->attributeProductWeight.'.value', 0);
                HRPochta::normalizeWeight($weight);
                
                $totalWeight+=$weight;
                
                $items[]=[
                    'weight'=>$weight,
                    'amount'=>(float)A::rget($item, 'count.value', 1),
                    'comment'=>mb_substr(A::rget($item, 'title.value', ''), 0, 255)
                ];
            }
            
            $this->owner->rpochta->mass=$totalWeight * 1000;
            $this->owner->rpochta->items=json_encode($items, JSON_UNESCAPED_UNICODE);
            
            $calcParams=[
                'mass'=>$this->owner->rpochta->mass,
                'mail_category'=>$this->owner->rpochta->rpo_category,
                'mail_type'=>$this->owner->rpochta->rpo_type,
                'payment_method'=>$this->owner->rpochta->payment_type
            ];
            if($rpochta->isOpsMode() && $this->owner->rpochta->ops_index) {
                $calcParams['index_to']=$this->owner->rpochta->ops_index;
            }
            else {
                $calcParams['index_to']=$this->owner->rpochta->index_to;
            }
            $calcResult=RPochtaApi::i()->tariff($calcParams, true);
            
            $this->owner->rpochta->delivery_price_data=json_encode($calcResult, JSON_UNESCAPED_UNICODE);
            if(!isset($calcResult['errors'])) {
                $this->owner->rpochta->delivery_origin_price=HRPochta::toRuble(A::rget($calcResult, 'result.origin-total-cost'));
                $this->owner->rpochta->delivery_price=HRPochta::toRuble(A::rget($calcResult, 'result.total-cost'));
            }
            
            if((float)HRPochta::settings()->rpochta_extra_charge > 0) {
                $this->owner->rpochta->delivery_extra_charge=HRPochta::settings()->rpochta_extra_charge;
            }
            
            $this->owner->rpochta->save();
        }
        
        return true;
    }
    
    protected function addErrors($model) 
    {
        foreach($model->getErrors() as $attribute=>$errors) {
            foreach($errors as $error) {
                $this->owner->addError('rpochta_errors', 'Почта.России: ' . $error);
            }
        }
    }
}
