<?php
/**
 * Файл настроек модели Статья
 */
use common\components\helpers\HYii as Y;

return [
	'class'=>'\Article',
	'menu'=>[
		'backend'=>['label'=>'Статьи']
	],
	'buttons'=>[
		'create'=>['label'=>'Добавить статью'],
	],
	'crud'=>[
		'index'=>[
            'url'=>'/cp/crud/index',
			'title'=>'Статьи',
			'gridView'=>[
				'dataProvider'=>[
					'criteria'=>[
						'select'=>'id, title, active, preview, preview_text, create_time',
						'order'=>'create_time DESC'
					]
				],
				'columns'=>[
					[
                        'name'=>'id',
                        'header'=>'#',
                        'headerHtmlOptions'=>['style'=>'width:5%'],
                    ],
 				 	[
                        'name'=>'preview_image',
                        'header'=>'Изображение',
                        'type'=>[
                            'common.ext.file.image'=>[
                                'behaviorName'=>'previewImageBehavior',
                                'width'=>120,
                                'height'=>120,
                                'htmlOptions'=>['style'=>'max-width:120px;']
                        ]],
                        'headerHtmlOptions'=>['style'=>'width:15%'],
                    ],
					[
						'name'=>'title',
						'header'=>'Наименование',
						'type'=>'raw',
						'value'=>'"<strong>".CHtml::link($data->title,["/cp/crud/update", "cid"=>"articles", "id"=>$data->id])."</strong><br/><small>"'
							. ' . "<b>Дата создания:</b> " . $data->getDate() . "<br/>"'
							. ' . $data->preview_text'
							. ' . "</small>"'
					],
					[
						'name'=>'active',
						'type'=>'common.ext.active',
						'header'=>'Опубликовать',
						'headerHtmlOptions'=>['style'=>'width:15%']
					],
					'crud.buttons'
				]
			]
		],
		'create'=>[
			'url'=>'/cp/crud/create',
			'title'=>'Новая статья',
		],
		'update'=>[
			'url'=>['/cp/crud/update'],
			'title'=>'Редактирование статьи',
		],
		'delete'=>[
            'url'=>['/cp/crud/delete'],
		],
		'form'=>[
            'htmlOptions'=>['enctype'=>'multipart/form-data'],
        ],
        'tabs'=>[
        	'main'=>[
        		'title'=>'Основные',
				'attributes'=>[
		            'active'=>'checkbox',
					'title',
					'alias'=>'alias',
					'create_time'=>'date',
		            'preview_image'=>[
		                'type'=>'common.ext.file.image',
		                'behaviorName'=>'previewImageBehavior',
		                'params'=>[
		                    'tagOptions'=>['class'=>'col-xs-12 panel panel-default'],
		                ]
		            ],
		            'preview_text'=>['type'=>'tinyMce', 'params'=>['full'=>false]],
		            'text'=>'tinyMce',
				]
			],
			'seo'=>[
				'title'=>'SEO',
				'attributes'=>[
					'meta_h1',
					'meta_title',
					'meta_key',
					'meta_desc'
				]
			]
		]
	],
];
