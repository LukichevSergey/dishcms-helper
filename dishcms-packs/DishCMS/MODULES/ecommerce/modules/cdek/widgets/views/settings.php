<?php
use common\components\helpers\HArray as A;
use cdek\models\Tariff;

?>
<div class="panel panel-default">
    <div class="panel-heading">Настройки СДЭК</div>
    <div class="panel-body"><?
    $this->owner->widget('\common\widgets\form\TextField', [
        'form'=>$this->form, 
        'model'=>$this->model,
        'attribute'=>'cdek_seller_name',
        'note'=>'* используется при печати заказов для отображения настоящего продавца товара, либо торгового названия',
        'noteOptions'=>['style'=>'font-size:0.85em;color:#777;'],
        'htmlOptions'=>['class'=>'w50 form-control']
    ]);
    
    $this->owner->widget('\common\widgets\form\TextField', [
        'form'=>$this->form, 
        'model'=>$this->model,
        'attribute'=>'cdek_send_city_id', 
        'htmlOptions'=>['class'=>'w10 inline form-control']
    ]);
    
    $this->owner->widget('\common\widgets\form\NumberField', [
        'form'=>$this->form, 
        'model'=>$this->model,
        'attribute'=>'cdek_package_item_cost', 
        'unit'=>' руб.',
        'note'=>'* за единицу товара в указанной валюте, значение >=0). С данного значения рассчитывается страховка.
        <br/>Если не будет задана или указано отрицательное значение, будет установлена стоимость самого товара.
        <br/>Для всех ранее оформленных заказов, объявленая стоимость пересчитана не будет.',
        'noteOptions'=>['style'=>'font-size:0.85em;color:#777;'],
        'htmlOptions'=>['class'=>'w10 inline form-control']
    ]);    

    $this->owner->widget('\common\widgets\form\NumberField', [
        'form'=>$this->form, 
        'model'=>$this->model,
        'attribute'=>'cdek_extra_charge', 
        'unit'=>'%',
        'htmlOptions'=>['class'=>'w10 inline form-control', 'step'=>0.1]
    ]);

    $this->owner->widget('\common\widgets\form\DropDownListField', [
        'form'=>$this->form, 
        'model'=>$this->model,
        'attribute'=>'cdek_tariff_group', 
        'data'=>Tariff::i()->groupLabels(),
        'htmlOptions'=>['class'=>'w50 inline form-control']
    ]);
    
    $this->owner->widget('\common\widgets\form\TextField', [
        'form'=>$this->form,
        'model'=>$this->model,
        'attribute'=>'cdek_ymap_apikey',
    ]);
    ?></div>
</div>
