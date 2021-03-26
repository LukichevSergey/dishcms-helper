<?php
/**
 * Поведение для модели формы покупателя для доставки Почта.России
 * 
 */
namespace rpochta\behaviors;

use common\components\helpers\HModel;
use rpochta\models\Order;

class CustomerFormBehavior extends \CBehavior
{
    public $rpochta_errors;
    public $attributeDeliveryType='delivery_type';
    public $deliveryTypeRPochtaValue='rpochta';
    
    public function events()
    {
        return [
            'onBeforeValidate'=>'beforeValidate',
        ];
    }
    
    public function attributeLabels()
    {
        return [
            'rpochta_errors'=>'Почта.России'
        ];
    }
    
    public function beforeValidate()
    {
        if($this->owner->{$this->attributeDeliveryType} != $this->deliveryTypeRPochtaValue) {
            return true;
        }
        
        $rpochta=HModel::massiveAssignment('\rpochta\models\Order', true);
        $rpochta->setScenario($rpochta->getScenarioByMode());
        
        $valided=$rpochta->validate();
        if(!$valided) {
            $this->addErrors($rpochta);
        }
        
        return $valided;
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
