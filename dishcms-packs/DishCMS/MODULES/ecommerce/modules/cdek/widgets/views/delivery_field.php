<?
use common\components\helpers\HArray as A;
?>
<div class="cdek__box">
    <?= $this->form->hiddenField($this->model, 'send_city_id'); ?>
    <div class="cdek__city row">
    	<label>Населенный пункт:</label>
        <? 
        $this->widget('common.widgets.chosen.Chosen', [                            
           'model'=>$this->model,
           'attribute'=>'rec_city_id',
           'placeholderSingle' => 'Выберите город доставки',
           'enableSplitWordSearch'=>false,
           'searchContains'=>false,
           'noResults'=>'Ни одного города не найдено',
           'data'=>$this->getCityData(),
           'options'=>['width'=>'70%'],
           'htmlOptions'=>['data-js'=>'cityname']
         ]); ?>
    </div>
    <div class="cdek__mode row">
        <label>Тип доставки:</label>
        <?=$this->form->radioButtonList($this->model, 'delivery_mode', $this->getDeliveryModes(), [
            'labelOptions'=>['class'=>'inline'],
            'data-js'=>'mode'
        ]); ?>
    </div>
    <div class="cdek__pvz row" data-js="pvz">
        <? $this->owner->widget('\cdek\widgets\PvzField', [
            'form'=>$this->form,
            'model'=>$this->model,
            'attribute'=>'pvz_code',
            'jPvzButton'=>'cdek-pvz-btn'
        ]); ?>
    </div>
    <div class="cdek__address row" data-js="address">
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
            'attribute'=>'address_flat',
            'tagOptions'=>['class'=>'row col-md-4']
        ]); ?>
    </div>
    <div class="cdek__info" style="display:none" data-js="info"></div>
</div>
