<?php
use common\components\helpers\HArray as A;
use ykassa\components\helpers\HYKassa;
$modeAttribute='mode';
\CHtml::resolveNameID($model, $modeAttribute, $modeHtmlOptions);
?>
<div class="panel panel-default">
	<div class="panel-heading">Тестовый режим (API) <label style="float:right">выбрать <?=\CHtml::radioButton($modeHtmlOptions['name'], HYKassa::isApiTestMode(), ['value'=>HYKassa::MODE_API_TEST, 'style'=>'position:relative;top:2px']); ?></label></div>
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
	<div class="panel-heading">Боевой режим (API) <label style="float:right">выбрать <?=\CHtml::radioButton($modeHtmlOptions['name'], HYKassa::isApiMode(), ['value'=>HYKassa::MODE_API, 'style'=>'position:relative;top:2px']); ?></label></div>
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
<? /* ?>
<div class="alert alert-danger">
        HTTP протокол является устаревшим и его использование не рекомендуется.
</div>
<div class="panel panel-default">
	<div class="panel-heading">Тестовый режим (HTTP) <label style="float:right">выбрать <?=\CHtml::radioButton($modeHtmlOptions['name'], HYKassa::isHttpTestMode(), ['value'=>HYKassa::MODE_HTTP_TEST, 'style'=>'position:relative;top:2px']); ?></label></div>
	<div class="panel-body">
	<?php 
        $this->widget('\common\widgets\form\TextField', A::m(compact('form', 'model'), [
            'attribute'=>'test_shop_id',
            'htmlOptions'=>['class'=>'form-control w50']
        ]));
        
        $this->widget('\common\widgets\form\TextField', A::m(compact('form', 'model'), [
            'attribute'=>'test_scid',
            'htmlOptions'=>['class'=>'form-control w50']
        ]));
        
        $this->widget('\common\widgets\form\PasswordField', A::m(compact('form', 'model'), [
            'attribute'=>'test_shop_password',
            'htmlOptions'=>['class'=>'form-control w50']
        ]));
	?>
	</div>
</div>
<div class="panel panel-info">
	<div class="panel-heading">Боевой режим (HTTP) <label style="float:right">выбрать <?=\CHtml::radioButton($modeHtmlOptions['name'], HYKassa::isHttpMode(), ['value'=>HYKassa::MODE_HTTP, 'style'=>'position:relative;top:2px']); ?></label></div>
	<div class="panel-body">
    <?
    
    $this->widget('\common\widgets\form\TextField', A::m(compact('form', 'model'), [
        'attribute'=>'shop_id',
        'htmlOptions'=>['class'=>'form-control w50']
    ]));
    
    $this->widget('\common\widgets\form\TextField', A::m(compact('form', 'model'), [
        'attribute'=>'scid',
        'htmlOptions'=>['class'=>'form-control w50']
    ]));
    
    $this->widget('\common\widgets\form\PasswordField', A::m(compact('form', 'model'), [
        'attribute'=>'shop_password',
        'htmlOptions'=>['class'=>'form-control w50']
    ]));
    
    ?>
    </div>
</div>
<? /**/ ?>