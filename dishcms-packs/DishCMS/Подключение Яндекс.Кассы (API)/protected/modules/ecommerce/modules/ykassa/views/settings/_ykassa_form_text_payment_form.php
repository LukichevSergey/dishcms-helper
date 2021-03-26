<?php
use common\components\helpers\HArray as A;

$this->widget('\common\widgets\form\TextField', A::m(compact('form', 'model'), [
    'attribute'=>'title_payment_form',
    'htmlOptions'=>['class'=>'form-control w50']
]));

$this->widget('\common\widgets\form\TinyMceField', A::m(compact('form', 'model'), [
    'attribute'=>'text_payment_form',
]));
?>