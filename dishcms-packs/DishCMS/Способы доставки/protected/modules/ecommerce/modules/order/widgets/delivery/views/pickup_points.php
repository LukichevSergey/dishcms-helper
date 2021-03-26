<?php
/** @var \ecommerce\modules\order\widgets\delivery\OrderCustomerField $this  */

use common\components\helpers\HYii as Y;
use crud\models\ar\ecommerce\order\models\DeliveryType;
use crud\models\ar\ecommerce\order\models\PickupPoint;

$model=$this->behavior->getOwner();
$deliveries=DeliveryType::model()->published()->bySort()->findAll();
$pickupPoints=PickupPoint::model()->published()->bySort()->findAll();
$isPickUp=$this->behavior->isPickUp();
?>
<div class="delivery-type__type">
<?
echo $this->form->radioButtonList($model, 'delivery_type_id', \CHtml::listData($deliveries, 'id', 'title'), [
    'class'=>'js-delivery-type',
    'template'=>'<div class="delivery-type__type-item">{input}&nbsp;{label}</div>',
    'separator'=>''
]);
echo $this->form->error($model, 'delivery_type_id');
?>
<div class="delivery-type__is-pickup js-delivery-type-is-pickup"<?if(!$model->delivery_type_id || !$isPickUp) echo ' style="display:none"'; ?>>
<?
echo $this->form->labelEx($model, 'delivery_pickup_point_id');
echo $this->form->dropDownList($model, 'delivery_pickup_point_id', \CHtml::listData($pickupPoints, 'id', 'title'), [
    'class'=>'js-delivery-pickup-point',
    'empty'=>'-- выберите пункт самовывоза --'
]);
echo $this->form->error($model, 'delivery_pickup_point_id');
?>
</div>
<div class="delivery-type__is-not-pickup js-delivery-type-is-not-pickup"<?if(!$model->delivery_type_id || $isPickUp) echo ' style="display:none"'; ?>>
    <div class="delivery-type__address-city">
    <?
    echo $this->form->labelEx($model, 'delivery_address_city');
    echo $this->form->textField($model, 'delivery_address_city');
    echo $this->form->error($model, 'delivery_address_city');
    ?>
    </div>
    <div class="delivery-type__address-sub">
    <div class="delivery-type__address-street">
        <?
        echo $this->form->labelEx($model, 'delivery_address_street');
        echo $this->form->textField($model, 'delivery_address_street');
        echo $this->form->error($model, 'delivery_address_street');
        ?>
    </div>
        <div class="delivery-type__address-house">
        <?
        echo $this->form->labelEx($model, 'delivery_address_house');
        echo $this->form->textField($model, 'delivery_address_house');
        echo $this->form->error($model, 'delivery_address_house');
        ?>
        </div>
        <div class="delivery-type__address-flat">
        <?
        echo $this->form->labelEx($model, 'delivery_address_flat');
        echo $this->form->textField($model, 'delivery_address_flat');
        echo $this->form->error($model, 'delivery_address_flat');
        ?>
        </div>
    </div>
</div>
<? 
$deliveryParamsJson=json_encode(array_map(function($delivery) {
    return [
        'id'=>$delivery->id, 
        'pickup'=>$delivery->isPickUp(), 
        'discount'=>$delivery->getDiscount()
    ];
}, $deliveries));
$jsCode=<<<EOL
let deliveryParams={$deliveryParamsJson};\$(document).on('change', '.js-delivery-type', function(e) {
let id=\$(e.target).closest('.js-delivery-type:checked').val(),box=\$('.js-delivery-type-is-not-pickup'),
pbox=\$('.js-delivery-type-is-pickup'), delivery=deliveryParams.filter(d => d.id == id);
if(!!delivery && delivery.length>0) {
    if(!!delivery[0] && !delivery[0].pickup){pbox.hide();box.show();}else{box.hide();pbox.show();}
    \$(window).trigger('onChangeOrderDelivery', {delivery:delivery[0]});
}});
\$(document).find('.js-delivery-type').trigger('change');
\$(window).on("onCartUpdateTotal", function(){
\$(document).find('.js-delivery-type').trigger('change');
});
EOL;
Y::js(null, $jsCode, \CClientScript::POS_READY);
Y::css(null, trim('
.delivery-type__type{width:100%}
.delivery-type__type-item{display:flex}
.delivery-type__type-item label{font-weight:normal}
.delivery-type__address-city{width:100%}
.delivery-type__address-sub{display:flex;width:100%;}
.delivery-type__address-street{margin-right:10px;width:100%;}
.delivery-type__address-house{margin-right:10px;max-width:100px;}
.delivery-type__address-flat{max-width:100px;}
.delivery-type__is-not-pickup{display:flex;flex-flow:wrap;flex-direction:row;}
'));
?>