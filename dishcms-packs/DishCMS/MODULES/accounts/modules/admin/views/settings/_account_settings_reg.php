<?php
/** @var \CActiveForm $form */
/** @var \accounts\models\AccountSettings $model */
use common\components\helpers\HArray as A;

$this->widget('\common\widgets\form\CheckboxField', A::m(compact('form', 'model'), [
    'attribute'=>'reg_confirm_mode',
    'note'=>'Для подтверждения регистрации будет выслано пользователю письмо со специальной ссылкой'
]));

$this->widget('\common\widgets\form\TinyMceField', A::m(compact('form', 'model'), [
    'attribute'=>'reg_done_text',
    'uploadImages'=>false,
    'uploadFiles'=>false,
    'showAccordion'=>false
]));