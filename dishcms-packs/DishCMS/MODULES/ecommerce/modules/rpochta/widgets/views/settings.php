<?php
use common\components\helpers\HArray as A;

?>
<div class="panel panel-default">
    <div class="panel-heading">Настройки для сервиса Почта.России</div>
    <div class="panel-body"><?
    
    $this->owner->widget('\common\widgets\form\TextField', [
        'form'=>$this->form, 
        'model'=>$this->model,
        'attribute'=>'rpochta_brand_name', 
        'htmlOptions'=>['class'=>'w50 inline form-control']
    ]);
    
    $this->owner->widget('\common\widgets\form\TextField', [
        'form'=>$this->form, 
        'model'=>$this->model,
        'attribute'=>'rpochta_index_from', 
        'htmlOptions'=>['class'=>'w10 inline form-control']
    ]);
    
    $this->owner->widget('\common\widgets\form\TextField', [
        'form'=>$this->form, 
        'model'=>$this->model,
        'attribute'=>'rpochta_index_from_name', 
        'htmlOptions'=>['class'=>'w50 inline form-control']
    ]);
    
    $this->owner->widget('\common\widgets\form\NumberField', [
        'form'=>$this->form, 
        'model'=>$this->model,
        'attribute'=>'rpochta_extra_charge', 
        'unit'=>'%',
        'htmlOptions'=>['class'=>'w10 inline form-control', 'step'=>0.1]
    ]);
    
    $this->owner->widget('\common\widgets\form\NumberField', [
        'form'=>$this->form, 
        'model'=>$this->model,
        'attribute'=>'rpochta_insr_value', 
        'unit'=>' коп.',
        'htmlOptions'=>['class'=>'w25 inline form-control']
    ]);
    ?>
    </div>
    <div class="panel-heading">Дополнительные параметры</div>
    <div class="panel-body">
    <?
    $this->owner->widget('\common\widgets\form\CheckboxField', [
        'form'=>$this->form, 
        'model'=>$this->model,
        'attribute'=>'rpochta_courier'
    ]);
    
    $this->owner->widget('\common\widgets\form\CheckboxField', [
        'form'=>$this->form, 
        'model'=>$this->model,
        'attribute'=>'rpochta_fragile'
    ]);
    
    $this->owner->widget('\common\widgets\form\CheckboxField', [
        'form'=>$this->form, 
        'model'=>$this->model,
        'attribute'=>'rpochta_with_order_of_notice'
    ]);
    
    $this->owner->widget('\common\widgets\form\CheckboxField', [
        'form'=>$this->form, 
        'model'=>$this->model,
        'attribute'=>'rpochta_with_simple_notice'
    ]);
    
    $this->owner->widget('\common\widgets\form\CheckboxField', [
        'form'=>$this->form, 
        'model'=>$this->model,
        'attribute'=>'rpochta_wo_mail_rank'
    ]);
    
    $this->owner->widget('\common\widgets\form\CheckboxField', [
        'form'=>$this->form, 
        'model'=>$this->model,
        'attribute'=>'rpochta_sms_notice_recipient'
    ]);
    ?>
    </div>
</div>
