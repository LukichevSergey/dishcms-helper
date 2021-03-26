<?php
/** @var \CActiveForm $form */
/** @var \accounts\models\AccountSettings $model */
use common\components\helpers\HArray as A;

$this->widget('\common\widgets\form\TinyMceField', A::m(compact('form', 'model'), [
    'attribute'=>'restore_form_text',
    'uploadImages'=>false,
    'uploadFiles'=>false,
    'showAccordion'=>false
]));

$this->widget('\common\widgets\form\TinyMceField', A::m(compact('form', 'model'), [
    'attribute'=>'restore_change_form_text',
    'uploadImages'=>false,
    'uploadFiles'=>false,
    'showAccordion'=>false
]));