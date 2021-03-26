<?php
use common\components\helpers\HArray as A;

$this->widget('\common\widgets\form\TextField', A::m(compact('form', 'model'), ['attribute'=>'notisend_apikey']));
$this->widget('\common\widgets\form\NumberField', A::m(compact('form', 'model'), ['attribute'=>'notisend_list_id', 'htmlOptions'=>['class'=>'form-control w25']]));
$this->widget('\common\widgets\form\CheckboxField', A::m(compact('form', 'model'), ['attribute'=>'notisend_unconfirmed']));