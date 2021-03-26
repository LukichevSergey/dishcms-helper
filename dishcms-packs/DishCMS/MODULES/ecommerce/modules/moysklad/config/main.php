<?php
return [
    'aliases'=>[
        'Psr'=>'ecommerce.modules.moysklad.vendors.Psr',
        'GuzzleHttp'=>'ecommerce.modules.moysklad.vendors.GuzzleHttp',
        'MoySklad'=>'ecommerce.modules.moysklad.vendors.MoySklad',
    ],
    'modules'=>[
        'admin'=>['class'=>'\ecommerce\modules\moysklad\modules\admin\AdminModule']
    ]
];