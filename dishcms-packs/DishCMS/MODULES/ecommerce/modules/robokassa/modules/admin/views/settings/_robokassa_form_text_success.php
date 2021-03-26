<?php
use common\components\helpers\HArray as A;

$this->widget('\common\widgets\form\TextField', A::m(compact('form', 'model'), [
    'attribute'=>'title_success',
    'htmlOptions'=>['class'=>'form-control w50']
]));

$this->widget('\common\widgets\form\TinyMceField', A::m(compact('form', 'model'), [
    'attribute'=>'text_success',
]));
?>