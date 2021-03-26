<?php
return [
	'aliases'=>[
		'slider'=>'extend.modules.slider'
	],
	'modules'=>[
		'admin'=>[
			'class'=>'\slider\modules\admin\AdminModule'
		]	
	],
	'controllerMap'=>[
		'default'=>[
			'class'=>'\slider\controllers\DefaultController'
		]	
	]
];