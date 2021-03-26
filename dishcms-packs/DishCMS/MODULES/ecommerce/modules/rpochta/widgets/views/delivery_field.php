<?
use common\components\helpers\HArray as A;
use rpochta\components\RPochtaConst;
?>
<div class="rpochta__box">
    <?= $this->form->hiddenField($this->model, 'index_from'); ?>
    <div class="rpochta__city row">
    	<label>Населенный пункт:</label>
        <? 
        $this->widget('common.widgets.chosen.Chosen', [                            
           'model'=>$this->model,
           'attribute'=>'index_to',
           'placeholderSingle' => 'Выберите город доставки',
           'enableSplitWordSearch'=>false,
           'searchContains'=>false,
           'noResults'=>'Ни одного города не найдено',
           'data'=>$this->getCityData(),
           'options'=>['width'=>'70%'],
           'htmlOptions'=>['data-js'=>'cityname']
         ]); ?>
    </div>
    
    <? foreach(['payment_type'=>'getPaymentTypes', 'rpo_category'=>'getRpoCategories', 'rpo_type'=>'getRpoTypes'] as $paramAttribute=>$paramMethod): ?>
        <? $data=call_user_func([$this->model, $paramMethod]); ?>
        <div class="rpochta__<?=$paramAttribute?> row rpochta__param"<? if(count($data) < 2) echo ' style="display:none !important"'?>>
            <? if(!$this->model->{$paramAttribute}) $this->model->{$paramAttribute}=key($data); ?>
            <?= $this->form->labelEx($this->model, $paramAttribute); ?>
            <?= $this->form->radioButtonList($this->model, $paramAttribute, $data, [
                'labelOptions'=>['class'=>'inline'],
                'data-js'=>$paramAttribute
            ]); ?>
            <?= $this->form->error($this->model, $paramAttribute); ?>
        </div>
    <? endforeach; ?>
    
    <? if($this->ops): ?>
    <div class="rpochta__mode row">
        <label>Тип доставки:</label>
        <?=$this->form->radioButtonList($this->model, 'delivery_mode', $this->getDeliveryModes(), [
            'labelOptions'=>['class'=>'inline'],
            'data-js'=>'mode'
        ]); ?>
    </div>
    <? else: ?>
    <div class="rpochta__mode row">
        <label>Тип доставки: До адреса покупателя</label>
        <?= $this->form->hiddenField($this->model, 'delivery_mode'); ?>
    </div>
    <? endif; ?>    
    
    <? if($this->ops): ?>
    <div class="rpochta__ops row" data-js="ops">
        <? $this->owner->widget('\rpochta\widgets\OpsField', [
            'form'=>$this->form,
            'model'=>$this->model,
            'attribute'=>'ops_address',
            'attributeIndex'=>'ops_index',
            'attributeLongitude'=>'ops_longitude',
            'attributeLatitude'=>'ops_latitude',
            'jOpsButton'=>'rpochta-ops-btn'
        ]); ?>
    </div>
    <? endif; ?>
    
    <div class="rpochta__address row" data-js="address">
        <? $this->widget('\common\widgets\form\TextField', [
            'form'=>$this->form,
            'model'=>$this->model,
            'attribute'=>'address_street',
            'tagOptions'=>['class'=>'row col-md-12']
        ]); ?>
        <? $this->widget('\common\widgets\form\TextField', [
            'form'=>$this->form,
            'model'=>$this->model,
            'attribute'=>'address_house',
            'tagOptions'=>['class'=>'row col-md-4']
        ]); ?>
        <? $this->widget('\common\widgets\form\TextField', [
            'form'=>$this->form,
            'model'=>$this->model,
            'attribute'=>'address_room',
            'tagOptions'=>['class'=>'row col-md-4']
        ]); ?>
    </div>
    
    <div class="rpochta__info row" style="display:none" data-js="info"></div>
</div>


<? /*
use common\components\helpers\HArray as A;
?>
<div class="rpochta__box">
    <div class="rpochta__city">
    	<label>Населенный пункт:</label>
        <? 
        $postcode=A::get($_REQUEST, 'rpochta_delivery_city');
        $this->widget('ecommerce.ext.chosen.Chosen', [                            
           'name' => 'rpochta_delivery_city', // input name
           'placeholderSingle' => 'Выберите город доставки',
           'enableSplitWordSearch'=>false,
           'searchContains'=>false,
           'noResults'=>'Ни одного города не найдено',
           'data'=>\cdek\models\City::model()->listData('fullname', [
           		'condition'=>$postcode?'postcode=:postcode':'center=1', 
           		'order'=>'cityname', 
           		'params'=>$postcode?[':postcode'=>$postcode]:[]
           	], null, 'postcode'),
           'options'=>['width'=>'70%'],
           'htmlOptions'=>['id'=>'rpochta_delivery_city', 'data-js'=>'cityname'],
           'value'=>$delivery_city
         ]); ?>
    </div>
    <div class="rpochta__param" style="display:none">
        <label>Способы оплаты:</label>
        <?=\CHtml::radioButtonList('rpochta_payment_type', 'CASHLESS', [
            'CASHLESS'=>'Безналичный расчет',
            //'STAMP'=>'Оплата марками',
            //'FRANKING'=>'Франкирование',
        ], [
            'labelOptions'=>['class'=>'inline'],
            'data-js'=>'payment_type'
        ]); ?>
    </div>
    <div class="rpochta__param" style="display:none">
        <label>Категория РПО:</label>
        <?=\CHtml::radioButtonList('rpochta_rpo_category', 'ORDINARY', [
            //'SIMPLE'=>'Простое',
            //'ORDERED'=>'Заказное',
            'ORDINARY'=>'Обыкновенное',
            //'WITH_DECLARED_VALUE'=>'С объявленной ценностью',
            //'WITH_DECLARED_VALUE_AND_CASH_ON_DELIVERY'=>'С объявленной ценностью и наложенным платежом',
        ], [
            'labelOptions'=>['class'=>'inline'],
            'data-js'=>'rpo_category'
        ]); ?>
    </div>
    <div class="rpochta__param">
        <label>Вид РПО:</label>
        <?=\CHtml::radioButtonList('rpochta_rpo_type', A::get($_COOKIE, 'rpochta_rpo_type', 'POSTAL_PARCEL'), [
            'POSTAL_PARCEL'=>'Посылка "нестандартная"',
            //'ONLINE_PARCEL'=>'Посылка "онлайн"',
            //'ONLINE_COURIER'=>'Курьер "онлайн"',
            'EMS'=>'Отправление EMS',
            'EMS_OPTIMAL'=>'EMS оптимальное',
            //'LETTER'=>'Письмо',
            //'BANDEROL'=>'Бандероль',
            //'BUSINESS_COURIER'=>'Бизнес курьер',
            //'BUSINESS_COURIER_ES'=>'Бизнес курьер экпресс',
            //'PARCEL_CLASS_1'=>'Посылка 1-го класса',
        ], [
            'labelOptions'=>['class'=>'inline'],
            'data-js'=>'rpo_type'
        ]); ?>
    </div>
    
    <?/*<div class="rpochta__info" style="display:none" data-js="info"></div>*/ /*?>
</div>
*/ ?>
