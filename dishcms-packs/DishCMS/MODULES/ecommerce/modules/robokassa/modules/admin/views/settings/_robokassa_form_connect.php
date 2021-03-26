<?php
use common\components\helpers\HArray as A;
use ecommerce\modules\robokassa\components\helpers\HRobokassa;
$modeAttribute='enable_test_mode';
\CHtml::resolveNameID($model, $modeAttribute, $modeHtmlOptions);
?>
<div class="panel panel-default">
	<div class="panel-heading">Тестовый режим <label style="float:right">выбрать <?=\CHtml::radioButton($modeHtmlOptions['name'], HRobokassa::isTestMode(), ['value'=>'1', 'style'=>'position:relative;top:2px']); ?></label></div>
	<div class="panel-body">
    <?php
        $this->widget('\common\widgets\form\TextField', A::m(compact('form', 'model'), [
            'attribute'=>'test_merchant_login',
            'htmlOptions'=>['class'=>'form-control w50']
        ]));

        $this->widget('\common\widgets\form\PasswordField', A::m(compact('form', 'model'), [
            'attribute'=>'test_password1',
            'htmlOptions'=>['class'=>'form-control w50']
        ]));
        
        $this->widget('\common\widgets\form\PasswordField', A::m(compact('form', 'model'), [
            'attribute'=>'test_password2',
            'htmlOptions'=>['class'=>'form-control w50']
        ]));
	?>
	</div>
</div>
<div class="panel panel-info">
	<div class="panel-heading">Боевой режим <label style="float:right">выбрать <?=\CHtml::radioButton($modeHtmlOptions['name'], !HRobokassa::isTestMode(), ['value'=>'0', 'style'=>'position:relative;top:2px']); ?></label></div>
	<div class="panel-body">
    <?    
    $this->widget('\common\widgets\form\TextField', A::m(compact('form', 'model'), [
        'attribute'=>'merchant_login',
        'htmlOptions'=>['class'=>'form-control w50']
    ]));
    
    $this->widget('\common\widgets\form\PasswordField', A::m(compact('form', 'model'), [
        'attribute'=>'password1',
        'htmlOptions'=>['class'=>'form-control w50']
    ]));
    
    $this->widget('\common\widgets\form\PasswordField', A::m(compact('form', 'model'), [
        'attribute'=>'password2',
        'htmlOptions'=>['class'=>'form-control w50']
    ]));
    ?>
    </div>
</div>