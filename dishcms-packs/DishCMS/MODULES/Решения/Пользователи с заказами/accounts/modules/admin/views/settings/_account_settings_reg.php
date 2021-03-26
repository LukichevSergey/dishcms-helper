<?php
/** @var \CActiveForm $form */
/** @var \accounts\models\AccountSettings $model */
use common\components\helpers\HArray as A;

$model->reg_confirm_mode=0;
echo $form->hiddenField($model, 'reg_confirm_mode', ['value'=>0]);
/*
$this->widget('\common\widgets\form\CheckboxField', A::m(compact('form', 'model'), [
    'attribute'=>'reg_confirm_mode',
    'note'=>'Для подтверждения регистрации будет выслано пользователю письмо со специальной ссылкой'
]));
/**/
$this->widget('\common\widgets\form\TextField', A::m(compact('form', 'model'), [
    'attribute'=>'privacy_link'
]));

$this->widget('\common\widgets\form\TinyMceField', A::m(compact('form', 'model'), [
    'attribute'=>'reg_done_text',
    'uploadImages'=>false,
    'uploadFiles'=>false,
    'showAccordion'=>false
]));

/*
$this->widget('\common\widgets\form\TextField', A::m(compact('form', 'model'), [
    'attribute'=>'terms_link'
]));
*/


$this->widget('\common\widgets\form\TinyMceField', A::m(compact('form', 'model'), [
    'attribute'=>'reg_form_text',
    'full'=>false
]));


