<?php
use common\components\helpers\HArray as A;

?>
<div class="panel panel-default">
    <div class="panel-heading">Настройки для сервиса доставки ПЭК</div>
    <div class="panel-body">
        <div class="row">
        <? 
            $this->form->labelEx($this->model, 'pecom_take_town');
            $this->widget('common.widgets.chosen.Chosen', [
               'model'=>$this->model,
               'attribute'=>'pecom_take_town',
               'placeholderSingle' => 'Выберите город доставки',
               'enableSplitWordSearch'=>false,
               'searchContains'=>false,
               'noResults'=>'Ни одного города не найдено',
               'data'=>$this->getCityData(),
               'options'=>['width'=>'50%'],
               'htmlOptions'=>['data-js'=>'cityname']
            ]); 
            $this->form->error($this->model, 'pecom_take_town');
        ?>
        </div>
    </div>    
</div>
