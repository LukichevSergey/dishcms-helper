<?php
/**
 * Поле выбора доставки
 */
namespace DOrder\widgets\delivery;

use common\components\helpers\HYii as Y;

class DeliveryTypeField extends \common\components\widgets\form\BaseField
{
    public $attribute='delivery_type';
    public $view='delivery_type_field';
    
    public $pickup=true;
    public $pickupLabel='Самовывоз';
    
    public $cdek=true;
    public $cdekLabel='Курьерская служба СДЭК';
    public $cdekTariffGroup=false;
    public $cdekTariffModes=false;
    
    public $rpochta=true;
    public $rpochtaLabel='Почта России';
    
    public $cookieName='delivery_type';
    
    public function init()
    {
        parent::init();
        
        $this->publish(true, false);
    }
    
    public function run()
    {
        if(!$this->model->{$this->attribute} && isset($_COOKIE[$this->cookieName])) {
            $this->model->{$this->attribute}=$_COOKIE[$this->cookieName];
        }
        
        $options=[
            'field_name'=>\CHtml::resolveName($this->model, $this->attribute),
            'cookie_name'=>$this->cookieName
        ];        
        Y::js(false, ';window.DOrder_widgets_DeliveryTypeField.init('.\CJavaScript::encode($options).');', \CClientScript::POS_READY);
        
        parent::run();
    }
    
    public function getDeliveryTypes()
    {
        $types=[];
        if($this->pickup) $types['pickup']=$this->pickupLabel;
        if($this->cdek) $types['cdek']=$this->cdekLabel;
        if($this->rpochta) $types['rpochta']=$this->rpochtaLabel;
        return $types;
    }
}
