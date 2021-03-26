<?php
/**
 * Поведение для модели формы покупателя для СДЕК доставки
 * 
 */
namespace cdek\behaviors;

use common\components\helpers\HModel;
use cdek\models\Order;

class CustomerFormBehavior extends \CBehavior
{
    public $cdek_errors;
    public $attributeDeliveryType='delivery_type';
    public $deliveryTypeCdekValue='cdek';
    
    public function events()
    {
        return [
            'onBeforeValidate'=>'beforeValidate',
        ];
    }
    
    public function attributeLabels()
    {
        return [
            'cdek_errors'=>'СДЭК'
        ];
    }
    
    public function beforeValidate()
    {
        if($this->owner->{$this->attributeDeliveryType} != $this->deliveryTypeCdekValue) {
            return true;
        }
        
        $cdek=HModel::massiveAssignment('\cdek\models\Order', true);
        $cdek->setScenario($cdek->getScenarioByMode());
        
        $valided=$cdek->validate();
        if(!$valided) {
            $this->addErrors($cdek);
        }
        
        return $valided;
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
