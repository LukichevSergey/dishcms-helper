<?php
/**
 * Файл настроек модели \extend\modules\buildings\models\Porch
 */
use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HRequest as R;

$onBeforeLoad=function() { R::e404(); };

return [
	'class'=>'\extend\modules\buildings\models\Porch',
	'menu'=>[
		'backend'=>['label'=>'Подъезды']
	],
	'buttons'=>[
		'create'=>['label'=>'Добавить подъезд']
	],	
	'crud'=>[	
	    'breadcrumbs'=>[
	        'Планировки'=>\Yii::app()->createUrl("/cp/buildings/index")
	    ],
		'index'=>[
		    // 'onBeforeLoad'=>$onBeforeLoad,
			'url'=>'/cp/crud/index',
			'title'=>'Подъезды',
			'gridView'=>[ 
				'dataProvider'=>[
					'criteria'=>[
						'select'=>'`t`.`id`, `t`.`number`, `t`.`title`, `t`.`published`',
					    'order'=>'number, title'
					]
				],
				/*'sortable'=>[
                    'url'=>'/cp/crud/sortableSave',
                    'category'=>'buildings_porches'
                ],*/
				'columns'=>[
					'id'=>[
						'name'=>'id',
						'header'=>'#',
						'headerHtmlOptions'=>['style'=>'width:5%;text-align:center'],
					],
				    'number'=>[
				        'name'=>'number',
				        'header'=>'Подъезд',
				        'headerHtmlOptions'=>['style'=>'width:60%;text-align:center'],
				        'type'=>'raw',
				        'value'=>'\CHtml::link(
                            $data->getNumberTitle() . ($data->title?"<br/><small>{$data->title}</small>":""),
                            ["/cp/crud/index", "cid"=>"buildings_floors", "porch"=>$data->id]
                        )'
				    ],					
 					'active'=>[
 						'name'=>'published',
 						'header'=>'Опубликовать',
 						'type'=>[
 							'common.ext.active'=>[
 								'behaviorName'=>'publishedBehavior',
 							] 
						],
 						'headerHtmlOptions'=>['style'=>'width:15%']
 					],
					'crud.buttons'=>[
						'type'=>'crud.buttons',
						'params'=>[
							'template'=>'{floors}&nbsp;{update}&nbsp;{delete}',
							'buttons'=>[
								'floors' => [
									'label'=>'<span class="glyphicon glyphicon-home"></span>',
									'url'=>'\Yii::app()->createUrl("/cp/crud/index", ["cid"=>"buildings_floors", "porch"=>$data->id])',
								    'options'=>['title'=>'Этажи', 'class'=>'btn btn-warning']
								],							    
							    'update'=>[
							        'options'=>['class'=>'btn btn-default'],
							    ],
							    'delete'=>[
							        'options'=>['class'=>'btn btn-danger'],
							    ]
							],
							'headerHtmlOptions'=>['style'=>'width:25%'],
						    'htmlOptions'=>['style'=>'text-align:right']
						]
					]					
				]
			]
		],
		'create'=>[
			'url'=>'/cp/crud/create',
			'title'=>'Добавление нового подъезда'
		],
		'update'=>[
			'url'=>'/cp/crud/update',
			'title'=>'Редактирование подъезда'
		],
		'delete'=>[
			'url'=>'/cp/crud/delete'
		],
		'form'=>[
			'attributes'=>[
				'published'=>'checkbox',
			    'number'=>[
			        'type'=>'number',
			        'params'=>[
			            'htmlOptions'=>['class'=>'form-control w10']
			        ]
			    ],
			    'title'
			]
		]
	]
];
