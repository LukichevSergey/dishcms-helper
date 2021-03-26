<?php
/**
 * Файл настроек модели \Rangeof
 */
use common\components\helpers\HYii as Y;

$t=Y::ct('models/rangeof');
return [
	'class'=>'\Rangeof',
	'menu'=>[
		'backend'=>['label'=>$t('backend.menu.item.label')]
	],
	'buttons'=>[
		'create'=>['label'=>$t('button.create')],
	],
	'crud'=>[
		'use'=>['extend.modules.pages.config.crud.page', 'crud'],
		'index'=>[
			'title'=>$t('page.index.title'),
			'gridView'=>[
				'dataProvider'=>[
					'criteria'=>[
						'select'=>'`t`.`id`, `t`.`sef`, `t`.`title`, `t`.`preview_text`, `t`.`published`, `t`.`update_time`, `t`.`preview_image`, `t`.`preview_image_enable`',
						'scopes'=>['select'=>'display_index_page'],
					]
				],
				'sortable'=>[
					'url'=>'/cp/crud/sortableSave',
					'category'=>'rangeof',
				],
				'columns.sort'=>['display_index_page', 'crud.buttons'],
				'columns.sort.reverse'=>true,
				'columns'=>[
					'display_index_page'=>[
						'name'=>'display_index_page',
						'header'=>$t('crud.index.gridView.columns.display_index_page.header'),
						'type'=>[
							'common.ext.active'=>[
 								'behaviorName'=>'displayIndexPageBehavior',
 							]
						],
						'headerHtmlOptions'=>['style'=>'width:12%']
					],
					'crud.buttons'=>[
						'type'=>'crud.buttons',
						'params'=>[
							'buttons'=>[
								'preview' => [
									'url'=>'\Yii::app()->createUrl("shop/filter", ["filter"=>["rangeof"=>$data->sef]])'
								]
							]
						]
					]
				]
			]			
		],
		'create'=>[
			'title'=>$t('page.create.title')
		],
		'tabs'=>[
			'main'=>[
				'attributes'=>[
					'attributes.sort'=>['published', 'display_index_page', 'title'],
					'display_index_page'=>'checkbox'
				]
			],
		]			
	]
];
