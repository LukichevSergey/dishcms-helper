<?php
/**
 * Файл настроек модели \Infrastructure
 */
use common\components\helpers\HYii as Y;

use extend\modules\regions\models\Region;

$defaultRegion=Region::getDefaultRegion(false);

$t=Y::ct('\extend\modules\regions\RegionsModule.crud', 'extend.regions');

return [
	'class'=>'\extend\modules\regions\models\Region',
	'menu'=>[
		'backend'=>['label'=>$t('menu.backend.label')]
	],
	'buttons'=>[
		'create'=>['label'=>$t('buttons.create.label')]
	],
	'crud'=>[
		'form'=>[
			'htmlOptions'=>['enctype'=>'multipart/form-data'],
			'attributes'=>[
				'active'=>'checkbox',
				'is_default'=>[
					'type'=>'checkbox',
					'params'=>[
						'note'=>($defaultRegion ? $t('crud.attributes.is_default.note', ['{current_default_title}'=>$defaultRegion->title]): '')
					]
				],
				'domain',
				'title'
			]
		],
		'index'=>[
			'url'=>'/cp/crud/index',
			'title'=>$t('page.index.title'), 
			'gridView'=>[
				'sortable'=>[
					'category'=>'regions',
					'url'=>'/cp/crud/sortableSave',
				],
				'dataProvider'=>['pagination'=>['pageSize'=>999999]],
				'columns'=>[
					[
						'name'=>'id',
						'header'=>'ID',
						'headerHtmlOptions'=>['style'=>'width:5%'],
					],
					[
						'name'=>'title',
						'header'=>$t('crud.index.gridView.columns.title.header'),
						'type'=>'raw',
						'value'=>'"<strong>".CHtml::link($data->title,["/cp/crud/update", "cid"=>"regions", "id"=>$data->id])."</strong>"'
							. ' . "<br/><small>Домен: " . \CHtml::link($data->domain, "http://".$data->domain, ["target"=>"_blank"]) . "</small>"'
					],
 					[
 						'name'=>'code',
 						'header'=>$t('crud.index.gridView.columns.code.header'),
 						'headerHtmlOptions'=>['style'=>'width:15%'],
 						'htmlOptions'=>['style'=>'text-align:center']
 					],
 					[
 						'name'=>'is_default',
 						'header'=>$t('crud.index.gridView.columns.is_default.header'),
 						'headerHtmlOptions'=>['style'=>'width:12%'],
 						'htmlOptions'=>['style'=>'text-align:center'],
 						'type'=>'raw',
 						'value'=>'($data->is_default ? "<span class=\"label label-success\">Да</span></small>" : "")'
 					],
 					[
 						'name'=>'active',
 						'header'=>$t('crud.index.gridView.columns.active.header'),
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
							'template'=>'{update}',
						]			
					]
				]
			]
		],
		'create'=>[
			'url'=>'/cp/crud/create',
			'title'=>$t('page.create.title'),
			'form'=>[
				'attributes'=>[
					'code'=>[
						'type'=>'text',
						'params'=>[
							'note'=>$t('crud.attributes.code.note')
						]
					],
				]
			]
		],
		'update'=>[
			'url'=>'/cp/crud/update',
			'title'=>$t('page.update.title'),
			'form'=>[
				'attributes'=>[
					'attributes.sort'=>['active','is_default','code'],
					'code'=>[
						'type'=>'text',
						'params'=>['htmlOptions'=>['class'=>'form-control', 'readonly'=>true]]
					],
				],
				'buttons'=>[
					'delete'=>($defaultRegion->id != $_GET['id'])
				]
			]
		],
		'delete'=>[
			'url'=>'/cp/crud/delete',
		],
	]
];
