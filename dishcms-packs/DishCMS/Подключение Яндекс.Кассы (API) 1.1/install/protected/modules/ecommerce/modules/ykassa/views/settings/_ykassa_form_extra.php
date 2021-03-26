<?php
use common\components\helpers\HArray as A;

$this->widget('\common\widgets\form\TextField', A::m(compact('form', 'model'), [
    'attribute'=>'btn_pay_label',
    'htmlOptions'=>['class'=>'form-control w50']
]));

$model->btn_pay_styles=implode(";\n", array_filter(array_map('trim', explode(";", $model->btn_pay_styles)),'strlen'));
$this->widget('\common\widgets\form\TextAreaField', A::m(compact('form', 'model'), [
    'attribute'=>'btn_pay_styles',
    'htmlOptions'=>['class'=>'form-control w50']
]));

?>