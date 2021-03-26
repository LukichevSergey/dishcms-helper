<?php
use common\components\helpers\HArray as A;
?>
<div class="panel panel-default">
	<div class="panel-heading">Тестовый режим</div>
	<div class="panel-body">
	<?php 
        $this->widget('\common\widgets\form\CheckboxField', A::m(compact('form', 'model'), ['attribute'=>'enable_test_mode']));
        
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
	<div class="panel-heading">Боевой режим</div>
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
