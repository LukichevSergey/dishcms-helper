<?php
return [
	'controllerMap'=>[
        'payment'=>[
			'class'=>'\ecommerce\modules\robokassa\controllers\PaymentController'
		],
	],
	'modules'=>[
        'admin'=>['class'=>'\ecommerce\modules\robokassa\modules\admin\AdminModule']
    ]
];
