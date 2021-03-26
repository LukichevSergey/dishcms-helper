<?php
use common\components\helpers\HArray as A;
use amocrm\components\helpers\HAmoCRM;

$customFieldsDataList=HAmoCRM::getCustomFieldsListData();

$this->widget('\common\widgets\form\DropDownListField', A::m(compact('form', 'model'), [
    'attribute'=>'phone_field_id',
    'data'=>$customFieldsDataList,
    'htmlOptions'=>['empty'=>'-- не указан --', 'class'=>'form-control w100']
]));

$this->widget('\common\widgets\form\DropDownListField', A::m(compact('form', 'model'), [
    'attribute'=>'email_field_id',
    'data'=>$customFieldsDataList,
    'htmlOptions'=>['empty'=>'-- не указан --', 'class'=>'form-control w100']
])); 

?>