<?php
/**
 * Виджет отображения спобоба доставки
 */
namespace DOrder\widgets\delivery;

use common\components\helpers\HYii as Y;

class DeliveryTypeView extends \common\components\base\Widget
{
    public $value;
    public $orderId;
    public $view='delivery_type_view';
    public $emptyValue='Не указано';
    
    public $pickup=true;
    public $pickupLabel='Самовывоз';
    
    public $cdek=true;
    public $cdekLabel='Курьерская служба СДЭК';
    public $cdekTariffGroup=false;
    public $cdekTariffModes=false;
    
    public $rpochta=true;
    public $rpochtaLabel='Почта России';
    
    public function getDeliveryTypes()
    {
        $types=[];
        if($this->pickup) $types['pickup']=$this->pickupLabel;
        if($this->cdek) $types['cdek']=$this->cdekLabel;
        if($this->rpochta) $types['rpochta']=$this->rpochtaLabel;
        return $types;
    }
}
