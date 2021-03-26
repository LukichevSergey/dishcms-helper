<?php
/** @var \CActiveForm $form */
/** @var \accounts\models\AccountSettings $model */
use common\components\helpers\HArray as A;

$this->widget('\common\widgets\form\TextField', A::m(compact('form', 'model'), [
    'attribute'=>'secret_key',
    'note'=>'Используется для повышения безопасности. Минимум <b>8</b> символов.'
]));