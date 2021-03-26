<?php
use common\components\helpers\HArray as A;
use ykassa\components\helpers\HYKassa;
$modeAttributeName=\CHtml::resolveName($model, $attribute='mode');
?>
<div class="panel panel-default">
	<div class="panel-heading">Тестовый режим (API) <label style="float:right">выбрать <?=\CHtml::radioButton($modeAttributeName, HYKassa::isApiTestMode(), ['value'=>HYKassa::MODE_API_TEST, 'style'=>'position:relative;top:2px']); ?></label></div>
	<div class="panel-body">
	<?php 
        $this->widget('\common\widgets\form\TextField', A::m(compact('form', 'model'), [
            'attribute'=>'api_test_shop_id',
            'htmlOptions'=>['class'=>'form-control w50']
        ]));
        
        $this->widget('\common\widgets\form\TextField', A::m(compact('form', 'model'), [
            'attribute'=>'api_test_secret_key',
            'htmlOptions'=>['class'=>'form-control w50']
        ]));
	?>
	</div>
</div>
<div class="panel panel-info">
	<div class="panel-heading">Боевой режим (API) <label style="float:right">выбрать <?=\CHtml::radioButton($modeAttributeName, HYKassa::isApiMode(), ['value'=>HYKassa::MODE_API, 'style'=>'position:relative;top:2px']); ?></label></div>
	<div class="panel-body">
    <?    
    $this->widget('\common\widgets\form\TextField', A::m(compact('form', 'model'), [
        'attribute'=>'api_shop_id',
        'htmlOptions'=>['class'=>'form-control w50']
    ]));
    
    $this->widget('\common\widgets\form\TextField', A::m(compact('form', 'model'), [
        'attribute'=>'api_secret_key',
        'htmlOptions'=>['class'=>'form-control w50']
    ]));
    ?>
    </div>
</div>