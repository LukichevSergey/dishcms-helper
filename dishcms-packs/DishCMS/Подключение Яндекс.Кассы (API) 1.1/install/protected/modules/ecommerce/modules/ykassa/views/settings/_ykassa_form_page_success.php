<?php
use common\components\helpers\HArray as A;

$this->widget('\common\widgets\form\TextField', A::m(compact('form', 'model'), [
    'attribute'=>'page_success_title',
    'htmlOptions'=>['class'=>'form-control w50']
]));

$this->widget('\common\widgets\form\TinyMceField', A::m(compact('form', 'model'), [
    'attribute'=>'page_success_text',
]));
?>