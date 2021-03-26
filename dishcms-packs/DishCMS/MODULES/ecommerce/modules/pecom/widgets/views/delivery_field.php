<?
use common\components\helpers\HArray as A;
use rpochta\components\RPochtaConst;
?>
<div class="pecom__box">
    <div class="pecom__city row">
        <? 
        $this->widget('common.widgets.chosen.Chosen', [
           'model'=>$this->model,
           'attribute'=>$this->attribute,
           'placeholderSingle' => 'Выберите город доставки',
           'enableSplitWordSearch'=>false,
           'searchContains'=>false,
           'noResults'=>'Ни одного города не найдено',
           'data'=>$this->getCityData(),
           'options'=>['width'=>'70%'],
           'htmlOptions'=>['data-js'=>'cityname']
         ]); ?>
    </div>
    <div class="pecom__info row" style="display:none" data-js="info"></div>
</div>
