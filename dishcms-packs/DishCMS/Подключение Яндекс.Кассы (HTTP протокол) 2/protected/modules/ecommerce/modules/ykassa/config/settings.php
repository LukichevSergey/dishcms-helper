<?php
use ykassa\components\helpers\HYKassa;

return [
    'ykassa'=>[
        'class'=>'\ykassa\models\YKassaSettings',
        'title'=>'Настройки Яндекс.Кассы',
        'menuItemLabel'=>'Яндекс.Касса',
        'breadcrumbs'=>HYKassa::isCustomForm() ? [
            'История платежей'=>'/cp/crud/index?cid=ykassa_custom_payments'
        ] : [],
        'viewForm'=>'ykassa.views.settings._ykassa_form'
    ],
];