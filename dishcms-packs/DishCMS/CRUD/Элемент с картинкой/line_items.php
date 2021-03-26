<?php
/**
 * Файл настроек модели
 */
return [
	'class'=>'\LineItem',
	'menu'=>[
		'backend'=>['label'=>'Блок под баннером']	
	],
	'buttons'=>[
		'create'=>['label'=>'Добавить элемент'],
	],
	'crud'=>[
		'index'=>[
			'url'=>['/cp/crud/index'],
			'title'=>'Блок под баннером',
			'gridView'=>[
				'dataProvider'=>[
					'criteria'=>[
						'select'=>'`t`.`id`, title, active, image'
					]						
				],
				'sortable'=>[
					'category'=>'line_items',
					'url'=>'/cp/crud/sortableSave',
					// 'selector'=>'.grid-view > table > tbody',
					// 'dataId'=>'id',
					// 'autosave'=>true,
				],
				'columns'=>[
					[
						'name'=>'id',
						'header'=>'#',
						'headerHtmlOptions'=>['style'=>'width:5%']
					],
					[
						'name'=>'image',
						'type'=>[
							'common.ext.file.image'=>[
								'behaviorName'=>'imageBehavior',
								'width'=>120,
								'height'=>120,
								'proportional'=>true, 
								'htmlOptions'=>[],
								'default'=>true
						]],
						'headerHtmlOptions'=>['style'=>'width:15%']
					],
					[
						'name'=>'title',
						'header'=>'Заголовок',
						'type'=>'raw',
						'value'=>'"<strong>".CHtml::link($data->title,["/cp/crud/update", "cid"=>"line_items", "id"=>$data->id])."</strong>"'
					],
					[
						'name'=>'active',
						'type'=>[
							'common.ext.active'=>[
								'behaviorName'=>'activeBehavior'		
							]								
						],
							 
					],
					[
						'type'=>'crud.buttons',
						'params'=>[]
					] 
				]
			]
		],
		'create'=>[
			'url'=>['/cp/crud/create'],
			'title'=>'Новый элемент',
		],
		'update'=>[
			'url'=>['/cp/crud/update'],
			'title'=>'Редактирование элемента',
		],
		'delete'=>[
			'url'=>['/cp/crud/delete'],
		],
		'form'=>[
			'htmlOptions'=>['enctype'=>'multipart/form-data'],
			'attributes'=>[
				'active'=>'checkbox',
				'title'=>'text',
				'image'=>[
					'type'=>'common.ext.file.image',
					'params'=>[
						// 'actionDelete'=>\Yii::app()->getController()->createAction('removeImage'), // необязательно, по умолчанию /crud/admin/default/removeImage
						'tmbWidth'=>200,
						'tmbHeight'=>200,
					]
				]
			]
		],
	],
];