<?php
/**
 * Файл настроек модели
 */
use common\components\helpers\HYii as Y;

return [
	'class'=>'\ProductColor',
	'menu'=>[
		'backend'=>['label'=>'Цвета']
	],
	'buttons'=>[
		'create'=>['label'=>'Добавить цвет'],
	],
	'crud'=>[
		'index'=>[
            'url'=>'/cp/crud/index',
			'title'=>'Цвета',
			'gridView'=>[
                'sortable'=>[
                    'url'=>'/cp/crud/sortableSave',
                    'category'=>'product_colors',
                ],
				'columns'=>[
                    [
                        'name'=>'image',
                        'type'=>[
                            'common.ext.file.image'=>[
                                'behaviorName'=>'imageBehavior',
                                'width'=>120,
                                'height'=>120
                        ]],
                        'headerHtmlOptions'=>['style'=>'width:15%'],
                    ],
					[
						'name'=>'title',
						'header'=>'Наименование',
						'type'=>'raw',
						'value'=>'"<strong>".CHtml::link($data->title,["/cp/crud/update", "cid"=>"product_colors", "id"=>$data->id])."</strong>"'
                            . '. ($data->hexcode ? "<br/>HEX: <small>{$data->hexcode}&nbsp;<i style=\"background:{$data->hexcode};padding:3px 5px\">&nbsp;&nbsp;&nbsp;</i></small>" : "")'
					],
					[
						'name'=>'active',
						'type'=>'common.ext.active'
					],
					'crud.buttons'
				]
			]
		],
		'create'=>[
			'url'=>'/cp/crud/create',
			'title'=>'Новый цвет',
		],
		'update'=>[
			'url'=>['/cp/crud/update'],
			'title'=>'Редактирование цвета',
		],
		'delete'=>[
            'url'=>['/cp/crud/delete'],
		],
		'form'=>[
            'htmlOptions'=>['enctype'=>'multipart/form-data'],
			'attributes'=>[
                'active'=>'checkbox',
				'title',
                'hexcode',
                'image'=>'common.ext.file.image'
			]
		]
	],
];
