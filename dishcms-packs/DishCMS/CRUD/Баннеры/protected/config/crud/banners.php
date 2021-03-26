<?php
/**
 * Файл настроек модели \Banner
 */
use common\components\helpers\HYii as Y;

return [
	'class'=>'\Banner',
	'menu'=>[
		'backend'=>['label'=>'Баннеры']
	],
	'buttons'=>[
		'create'=>['label'=>'Добавить баннер']
	],	
	'crud'=>[		
		'index'=>[
			'url'=>'/cp/crud/index',
			'title'=>'Баннеры',
			'gridView'=>[ 
				'sortable'=>[
                    'url'=>'/cp/crud/sortableSave',
                    'category'=>'banners'
                ],
				'columns'=>[
					'id'=>[
						'name'=>'id',
						'header'=>'#',
						'headerHtmlOptions'=>['style'=>'width:5%;text-align:center'],
					],
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
					'title'=>[
						'name'=>'title',
						'header'=>'Наименование',
						'type'=>'raw',
						'value'=>'"<strong>".CHtml::link($data->title,["/cp/crud/update", "cid"=>"banners", "id"=>$data->id])."</strong><br/><small>"'
							. '. "Ссылка: " . ($data->link ?: "<i>не указана</i>")'
							. '. "</small>"'
					],
 					'active'=>[
 						'name'=>'active',
 						'header'=>'Отображать',
 						'type'=>[
 							'common.ext.active'=>[
 								'behaviorName'=>'activeBehavior',
 							] 
						],
 						'headerHtmlOptions'=>['style'=>'width:15%']
 					],
					'crud.buttons'		
				]
			]
		],
		'create'=>[
			'url'=>'/cp/crud/create',
			'title'=>'Новый баннер'
		],
		'update'=>[
			'url'=>'/cp/crud/update',
			'title'=>'Редактирование баннера'
		],
		'delete'=>[
			'url'=>'/cp/crud/delete'
		],
		'form'=>[
			'htmlOptions'=>['enctype'=>'multipart/form-data'],
			'attributes'=>[
				'active'=>'checkbox',
				'title',
				'link',
				'image'=>[
                        'type'=>'common.ext.file.image',
                        'behaviorName'=>'imageBehavior',
                        'params'=>[
                            'tagOptions'=>['class'=>'col-xs-12 panel panel-default'],
                            'tmbWidth'=>400,
                            'tmbHeight'=>200,
                            'tmbProportional'=>true
                        ]
                    ],

			],
		]
	]
];

