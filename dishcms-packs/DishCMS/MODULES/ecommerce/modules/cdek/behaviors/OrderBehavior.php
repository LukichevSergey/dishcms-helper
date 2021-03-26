<?php
/**
 * Поведение для заказа СДЕК доставки
 * 
 */
namespace cdek\behaviors;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HDb;
use common\components\helpers\HModel;
use cdek\components\CdekApi;
use cdek\components\helpers\HCdek;
use cdek\models\Tariff;
use cdek\models\Order;
use cdek\models\City;

class OrderBehavior extends \CBehavior
{
    public $attributeProductWeight='weight';
    public $attributeProductVolume='volume';
    public $attributeDeliveryType='delivery_type';
    public $deliveryTypeCdekValue='cdek';
    
    /**
     * @var float|false объявленная стоимость товара
     */
    public $packageItemCost=false;
    
    public $cdek_errors;
    
    public function relations()
    {
        return [
            'cdek'=>[\CActiveRecord::HAS_ONE, '\cdek\models\Order', 'order_id']
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
            'cdek_errors'=>'СДЭК'
        ];
    }
    
    public function isCdekDeliveryType()
    {
        $customer=$this->owner->getCustomerData();
        $deliveryType=A::rget($customer, $this->attributeDeliveryType.'.value');
        return ($deliveryType == $this->deliveryTypeCdekValue);
    }
    
    public function beforeValidate()
    {
        if(!$this->isCdekDeliveryType() || !in_array($this->owner->scenario, ['insert', 'create'])) {
            return true;
        }
        
        $this->owner->cdek=HModel::massiveAssignment('\cdek\models\Order', true);
        $this->owner->cdek->setScenario($this->owner->cdek->getScenarioByMode());        
        
        $valided=$this->owner->cdek->validate();
        if(!$valided) {
            $this->addErrors($this->owner->cdek);
        }
        
        return $valided;
    }
    
    public function afterSave()
    {
        $cdek=$this->owner->cdek;
        
        if($cdek->isNewRecord && $this->isCdekDeliveryType()) {
            $this->owner->cdek->order_id=$this->owner->id;
            $this->owner->cdek->order_number=$this->owner->hash;
            $this->owner->cdek->create_time=new \CDbExpression('NOW()');
            $this->owner->cdek->status=$cdek::STATUS_WAIT;
            
            // установка данных получателя
            $customer=$this->owner->getCustomerData();
            $this->owner->cdek->rec_name=trim(A::rget($customer, 'lastname.value') . ' ' . A::rget($customer, 'name.value') . ' ' . A::rget($customer, 'midname.value'));
            $this->owner->cdek->rec_email=A::rget($customer, 'email.value');
            $this->owner->cdek->rec_phone=preg_replace('/[^+0-9]+/', '', A::rget($customer, 'phone.value'));
            
            // получение дополнительной информации о городах отправителя и получателя.
            if($sendCity=City::model()->wcolumns(['cdek_id'=>$cdek->send_city_id])->find()) {
                $this->owner->cdek->send_city_name=$sendCity->cityname;
                $this->owner->cdek->send_city_postcode=$sendCity->postcode;
            }
            
            if($recCity=City::model()->wcolumns(['cdek_id'=>$cdek->rec_city_id])->find()) {
                $this->owner->cdek->rec_city_name=$recCity->cityname;
                $this->owner->cdek->rec_city_postcode=$recCity->postcode;
            }
            
            // получение дополнительной информации о ПВЗ
            if($cdek->isPvzMode()) {
                $pvzData=CdekApi::i()->getPvzList($cdek->rec_city_id, true);
                if(isset($pvzData[$cdek->pvz_code])) { 
                    $this->owner->cdek->pvz_data=json_encode($pvzData[$cdek->pvz_code], JSON_UNESCAPED_UNICODE);
                }
            }
            
            $this->owner->cdek->package_number=$this->owner->id;
            $this->owner->cdek->package_barcode=$this->owner->id;
            
            $items=[];
            $wareKey=1;
            $totalWeight=0;
            $orderItems=$this->owner->getOrderData();
            foreach($orderItems as $item) {
                $weight=A::rget($item, $this->attributeProductWeight.'.value', 0);
                $volume=A::rget($item, $this->attributeProductVolume.'.value', 0);
                HCdek::normalizeSize($weight, $volume);
                
                if($this->packageItemCost === false) {
                    $cost=HCdek::settings()->cdek_package_item_cost;
                    if(((float)$cost > 0) || (is_numeric($cost) && ((float)$cost===0))) {
                        $cost=(float)$cost;
                    }
                    else {
                        $cost=(float)A::rget($item, 'price.value', 0);
                    }
                }
                else {
                    $cost=$this->packageItemCost;
                }
                
                $totalWeight+=$weight;
                $items[$wareKey]=[
                    'WareKey'=>$wareKey,
                    'Cost'=>$cost,
                    // Payment. Оплата за товар при получении (за единицу товара в указанной валюте, значение >=0) — наложенный платеж, в случае предоплаты значение = 0.
                    'Payment'=>0,
                    //'PaymentVATRate'=>0,
                    //'PaymentVATSum'=>0,
                    'Weight'=>$weight,
                    'Volume'=>$volume,
                    'Amount'=>(float)A::rget($item, 'count.value', 1),
                    'Comment'=>mb_substr(A::rget($item, 'title.value', ''), 0, 255)
                ];
                
                $wareKey++;
            }
            
            $this->owner->cdek->package_weight=$totalWeight;
            $this->owner->cdek->items=json_encode($items, JSON_UNESCAPED_UNICODE);
            
            $calcResult=CdekApi::calc([
                'rec_city_id'=>$cdek->rec_city_id,
                'mode'=>$cdek->delivery_mode
            ]);
            
            if(isset($calcResult['result']['result']['tariffId'])) {
                $this->owner->cdek->tariff_id=$calcResult['result']['result']['tariffId'];
            }
            if(isset($calcResult['result']['result']['priceByCurrency'])) {
                $this->owner->cdek->delivery_price=$calcResult['result']['result']['priceByCurrency'];
            }
            if((float)HCdek::settings()->cdek_extra_charge > 0) {
                $this->owner->cdek->delivery_extra_charge=HCdek::settings()->cdek_extra_charge;
            }
            $this->owner->cdek->info=json_encode($calcResult, JSON_UNESCAPED_UNICODE);
            
            $this->owner->cdek->save();
        }
        
        return true;
    }
    
    protected function addErrors($model) 
    {
        foreach($model->getErrors() as $attribute=>$errors) {
            foreach($errors as $error) {
                $this->owner->addError('cdek_errors', 'СДЭК: ' . $error);
            }
        }
    }
}
