<?php
return [
	'aliases'=>[
		'YandexCheckout'=>'ykassa.vendor.YandexCheckout'
	],
	'controllerMap'=>[
        'http'=>[
			'class'=>'\ykassa\controllers\HttpPaymentController'
		],
        'httpPaymentCustom'=>[
			'class'=>'\ykassa\controllers\HttpPaymentCustomController'
		],
	]
];
