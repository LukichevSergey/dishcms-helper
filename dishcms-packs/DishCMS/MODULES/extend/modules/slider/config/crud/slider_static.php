<?php
/**
 * Файл настроек модели \slider\models\Slider
 */
use common\components\helpers\HYii as Y;
use extend\modules\slider\models\Slider;
use common\components\helpers\HRequest;

$extraAccess=D::role('sadmin');
$onBeforeLoad=function() use ($extraAccess) { if(!$extraAccess) HRequest::e404(); };

$t=Y::ct('\extend\modules\slider\SliderModule.crud', 'extend.slider');
return [
	'use'=>['extend.modules.slider.config.crud.slider', null],
	'buttons'=>[
		'create'=>['label'=>$extraAccess?$t('slider.button.create'):''],
	],	
	'crud'=>[		
		'index'=>[
			'gridView'=>[ 
				'columns'=>[
					'crud.buttons'=>[
						'params'=>[
							'template'=>$extraAccess?'{edit_slides}&nbsp;&nbsp;{update}{delete}':'{edit_slides}'
						]
					]
				]
			]
		],
		'create'=>[
			'onBeforeLoad'=>$onBeforeLoad
		],
		'update'=>[
			'onBeforeLoad'=>$onBeforeLoad
		],
		'delete'=>[
			'onBeforeLoad'=>$onBeforeLoad
		],
	]
];