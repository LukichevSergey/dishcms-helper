<?php
use ykassa\components\helpers\HYKassa;
use crud\models\ar\Region;

return [
    'ykassa'=>[
        'class'=>'\ykassa\models\YKassaSettings',
        'title'=>'Настройки Яндекс.Кассы' 
            . ((class_exists('\crud\models\ar\Region') && Region::getCurrentRegion()) ? (' (' . Region::getCurrentRegion()->title . ')') : ''),
        'menuItemLabel'=>'Яндекс.Касса',
        'breadcrumbs'=>HYKassa::isCustomForm() ? [
            // 'История платежей'=>'/cp/crud/index?cid=ykassa_custom_payments'
        ] : [
            'История платежей'=>'/cp/crud/index?cid=ykassa_history'
        ],
        'viewForm'=>'ykassa.views.settings._ykassa_form'
    ],
];