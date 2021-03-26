<?php

return [
    'robokassa'=>[
        'class'=>'\ecommerce\modules\robokassa\models\RobokassaSettings',
        'title'=>'Настройки Робокассы',
        'menuItemLabel'=>'Робокасса',
        'breadcrumbs'=>[
            'История платежей'=>'/cp/crud/index?cid=robokassa_payments'
        ],
        'viewForm'=>'ecommerce.modules.robokassa.modules.admin.views.settings._robokassa_form'
    ],
];