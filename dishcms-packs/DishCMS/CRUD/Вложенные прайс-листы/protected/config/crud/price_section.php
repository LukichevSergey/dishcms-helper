<?php
/**
 * Файл настроек модели \PriceSection
 */
use common\components\helpers\HYii as Y;

return [
	'class'=>'\PriceSection',
	'menu'=>[
		'backend'=>['label'=>'Прайс-лист']
	],
	'buttons'=>[
		'create'=>['label'=>'Добавить раздел']
	],	
	'crud'=>[		
		'index'=>[
			'url'=>'/cp/crud/index',
			'title'=>'Список основных разделов',
			'gridView'=>[ 
				'sortable'=>[
                    'url'=>'/cp/crud/sortableSave',
                    'category'=>'price_sections'
                ],
				'columns'=>[
					'id'=>[
						'name'=>'id',
						'header'=>'#',
						'headerHtmlOptions'=>['style'=>'width:5%;text-align:center'],
					],
					'title'=>[
						'name'=>'title',
						'header'=>'Наименование',
						'type'=>'raw',
						'value'=>'"<strong>".CHtml::link($data->title,["/cp/crud/index", "cid"=>"price_subsection", "section"=>$data->id])."</strong>"'
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
					'crud.buttons'=>[
						'type'=>'crud.buttons',
						'params'=>[
							'template'=>'{edit_subsections}&nbsp;&nbsp;{update}{delete}',
							'buttons'=>[
								'edit_subsections' => [
									'label'=>'<span class="glyphicon glyphicon-list-alt"></span>',
									'url'=>'\Yii::app()->createUrl("/cp/crud/index", ["cid"=>"price_subsection", "section"=>$data->id])',
									'options'=>['title'=>'Редатировать прайс-листы'],
								],
							],
							'headerHtmlOptions'=>['style'=>'width:10%']
						]
					]					
				]
			]
		],
		'create'=>[
			'url'=>'/cp/crud/create',
			'title'=>'Добавить основной раздел'
		],
		'update'=>[
			'url'=>'/cp/crud/update',
			'title'=>'Редактирование основного раздела'
		],
		'delete'=>[
			'url'=>'/cp/crud/delete'
		],
		'form'=>[
			'attributes'=>[
				'active'=>'checkbox',
				'title',
			],
		]
	]
];
