<?php
/**
 * Файл настроек модели
 */
use common\components\helpers\HYii as Y;

return [
	'class'=>'\ProductSizes',
	'menu'=>[
		'backend'=>['label'=>'Размеры']
	],
	'buttons'=>[
		'create'=>['label'=>'Добавить размер'],
	],
	'crud'=>[
		'index'=>[
            'url'=>'/cp/crud/index',
			'title'=>'Размеры',
			'gridView'=>[
				'sortable'=>[
                    'url'=>'/cp/crud/sortableSave',
                    'category'=>'product_sizes',
                ],
				'columns'=>[
					[
						'name'=>'title',
						'header'=>'Наименование',
						'type'=>'raw',
						'value'=>'"<strong>".CHtml::link($data->title,["/cp/crud/update", "cid"=>"product_sizes", "id"=>$data->id])."</strong>"'
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
			'title'=>'Новый размер',
		],
		'update'=>[
			'url'=>['/cp/crud/update'],
			'title'=>'Редактирование размера',
		],
		'delete'=>[
            'url'=>['/cp/crud/delete'],
		],
		'form'=>[
			'attributes'=>[
                'active'=>'checkbox',
				'title',
			]
		]
	],
];
