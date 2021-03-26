<?php
return [
    'aliases'=>[
        'AmoCRM'=>'application.modules.amocrm.vendor.AmoCRM',
        'League'=>'application.modules.amocrm.vendor.League',
        'Psr'=>'application.modules.amocrm.vendor.Psr',
        'GuzzleHttp'=>'application.modules.amocrm.vendor.GuzzleHttp',
    ],
    'modules'=>[
        'admin'=>['class'=>'\amocrm\modules\admin\AdminModule']
    ]
];