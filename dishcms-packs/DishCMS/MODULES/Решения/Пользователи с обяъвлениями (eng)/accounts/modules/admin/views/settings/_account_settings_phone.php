<?php
/** @var \CActiveForm $form */
/** @var \accounts\models\AccountSettings $model */
use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;

echo \CHtml::openTag('div', ['class'=>'account__settings-tab-phone']);

$this->widget('zii.widgets.jui.CJuiTabs', [
    'tabs'=>[
        $model->getAttributeLabel('phone_country_codes')=>[
            'content'=>$this->widget('\common\widgets\form\CheckboxListField', A::m(compact('form', 'model'), [
                'attribute'=>'phone_country_codes',
                'data'=>$model->getAllPhoneCountryCodes()
            ]), true), 
            'id'=>'tab-phone-codes'            
        ],
        $model->getAttributeLabel('phone_country_codes_preferrer')=>[
            'content'=>$this->widget('\common\widgets\form\CheckboxListField', A::m(compact('form', 'model'), [
                'attribute'=>'phone_country_codes_preferrer',
                'data'=>$model->getAllPhoneCountryCodes()
            ]), true), 
            'id'=>'tab-phone-codes-preferrer'
        ],
    ],
    'options'=>[]
]);

echo \CHtml::closeTag('div');

Y::css('account-settings-phone-tab', '.account__settings-tab-phone .ui-widget-header li a{font-size:12px;width:350px;white-space:normal;}');