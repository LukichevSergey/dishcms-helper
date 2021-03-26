<?php
return [
	'aliases'=>[
		'pages'=>'extend.modules.slider'
	],
	'modules'=>[
		'admin'=>[
			'class'=>'\pages\modules\admin\AdminModule'
		]	
	],
	'controllerMap'=>[
		'default'=>[
			'class'=>'\pages\controllers\DefaultController'
		]	
	]
];