<?php
use common\components\helpers\HArray as A;

if(D::isDevMode()) {
    $this->widget('\common\widgets\form\CheckboxField', A::m(compact('form', 'model'), ['attribute'=>'enable_debug_mode']));

    echo \CHtml::tag('div', ['class'=>'alert alert-danger'], 'Не забудьте отключить режим отладки!');

    $this->widget('\common\widgets\form\TextField', A::m(compact('form', 'model'), [
        'attribute'=>'api_default_config',
        'htmlOptions'=>['class'=>'form-control w50']
    ]));

    $this->widget('\common\widgets\form\TextAreaField', A::m(compact('form', 'model'), [
        'attribute'=>'online_payment_types',
        'htmlOptions'=>['class'=>'form-control w50']
    ]));
}