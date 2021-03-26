<?php
/**
 * Обновление геоданных городов
 */
use cdek\models\City;

$notUpdatedCityCount=City::model()->count([
    'condition'=>'`ym_point_x` IS NULL'
]);

if($notUpdatedCityCount > 0):
?>
<div class="alert alert-success">
    Не все геокоординаты городов обновлены.<br/>
    Кол-во городов для обновления: <strong><?=$notUpdatedCityCount?></strong><br/>
    Для увеличения быстродействия рекомендуется обновить геоданные вручную:<br/>
    <?= CHtml::ajaxLink('Обновить геоданные городов', '/ecommerce/cdek/admin/default/updateGeoCodes', [
        'type'=>'post',
        'dataType'=>'json',
        'beforeSend'=>'js:function(){
            $btn=$("#geocodes-update-btn");
            $btn.button("loading");
            return true;
        }',        
        'success'=>'js:function(response) {
            window.location.reload();
        }'
    ], [
       'class'=>'btn btn-primary', 
       'style'=>'margin-top: 10px',
       'id'=>'geocodes-update-btn',
       'data-loading-text'=>'Идет обновление...'
    ]);
    ?>
</div>
<?
else:
?>
<div class="alert alert-success">Все геокоординаты городов обновлены</div>
<?
endif;
?>
