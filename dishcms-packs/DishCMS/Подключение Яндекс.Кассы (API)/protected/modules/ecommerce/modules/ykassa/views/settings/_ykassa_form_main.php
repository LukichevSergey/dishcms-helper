<?php
use common\components\helpers\HArray as A;
use ykassa\components\helpers\HYKassa;

$this->widget('\common\widgets\form\DropDownListField', A::m(compact('form', 'model'), [
    'attribute'=>'tax',
    'data'=>HYKassa::getTaxList(),
    'htmlOptions'=>['class'=>'form-control w50', 'empty'=>'-- выберите ставку НДС --'],
    'note'=>'Если параметр будет не указан, форма оплаты отображаться не будет! . Используется для формирования чеков.'
]));

$this->widget('\common\widgets\form\DropDownListField', A::m(compact('form', 'model'), [
    'attribute'=>'tax_system',
    'data'=>HYKassa::getTaxSystemList(),
    'htmlOptions'=>['class'=>'form-control w50', 'empty'=>'-- не используется --'],
    'note'=>'Параметр необходим, только если у вас несколько систем налогообложения. Используется для формирования чеков'
]));

$this->widget('\common\widgets\form\DropDownListField', A::m(compact('form', 'model'), [
    'attribute'=>'payment_subject_type',
    'data'=>HYKassa::getPaymentSubjectTypeList(),
    'htmlOptions'=>['class'=>'form-control w50'],
]));

$this->widget('\common\widgets\form\DropDownListField', A::m(compact('form', 'model'), [
    'attribute'=>'payment_method_type',
    'data'=>HYKassa::getPaymentMethodTypeList(),
    'htmlOptions'=>['class'=>'form-control w50'],
]));
/*
if(HYKassa::isCustomForm()) {
    $this->widget('\common\widgets\form\TextField', A::m(compact('form', 'model'), [
        'attribute'=>'custom_product_title',
        'htmlOptions'=>['class'=>'form-control w50']
    ]));    
}
else {
    $this->widget('\common\widgets\form\TextField', A::m(compact('form', 'model'), [
        'attribute'=>'order_form_payment_type',
        'htmlOptions'=>['class'=>'form-control w50'],
        'note'=>'Если указанное значение будет не соответствовать значению поля <code>Способ оплаты</code> для онлайн-платежа, то возможность онлайн-оплаты доступна не будет!'
    ]));
}
*/
?>
