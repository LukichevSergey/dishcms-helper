<?
use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;

echo $this->form->radioButtonList($this->model, $this->attribute, $this->getDeliveryTypes(), ['labelOptions'=>['class'=>'inline']]); 

if($this->cdek) {
    echo \CHtml::tag('div', ['id'=>'delivery_type-cdek', 'style'=>'display:none;margin-top:5px;width:100%;'], $this->owner->widget('\cdek\widgets\DeliveryField', [
        'form'=>$this->form,
        'tariffGroup'=>$this->cdekTariffGroup,
        'tariffModes'=>$this->cdekTariffModes,
    ], true));
}
if($this->rpochta) {
    echo \CHtml::tag('div', ['id'=>'delivery_type-rpochta', 'style'=>'display:none;margin-top:5px;width:100%;'], $this->owner->widget('\rpochta\widgets\DeliveryField', [
        'form'=>$this->form,
    ], true));
}
?>
