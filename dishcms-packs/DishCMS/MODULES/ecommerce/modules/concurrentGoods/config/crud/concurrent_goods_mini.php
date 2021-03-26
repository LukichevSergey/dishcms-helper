<?php
/**
 * Файл настроек модели \ecommerce\modules\concurrentGoods\models\ConcurrentGoods
 * Сокращенный.
 */
use common\components\helpers\HYii as Y;

/** @var string $cid индетификатор настроек CRUD для модели. */
$cid=Y::requestGet('cid', 'ecommerce_concurrent_goods_mini');

$t=Y::ct('\ecommerce\modules\concurrentGoods\ConcurrentGoodsModule.crud', 'ecommerce.concurrentGoods');
return [
	'class'=>'\ecommerce\modules\concurrentGoods\models\ConcurrentGoods',
	'menu'=>[
		'backend'=>['label'=>$t('backend.menu.item.label')]
	],
	'buttons'=>[
		'create'=>['label'=>$t('button.create')],
	],
	'crud'=>[
		'form'=>[
			'htmlOptions'=>['enctype'=>'multipart/form-data']
		],
		'index'=>[
			'url'=>'/cp/crud/index',
			'title'=>$t('page.index.title'),
			'gridView'=>[ 
				'dataProvider'=>[
					'criteria'=>[
						'select'=>'`t`.`id`, `t`.`title`, `t`.`price`, `t`.`preview_text`, `t`.`active`, `t`.`update_time`, `t`.`preview_image`, `t`.`preview_image_enable`',
					]
				],
				'sortable'=>[
					'url'=>'/cp/crud/sortableSave',
					'category'=>'ecommerce_concurrent_goods',
				],
				'columns'=>[
					'id'=>[
						'name'=>'id',
						'header'=>'#',
						'headerHtmlOptions'=>['style'=>'width:5%'],
					],
					/*'preview_image'=>[
						'name'=>'preview_image',
						'header'=>$t('crud.index.gridView.columns.preview_image.header'),
						'type'=>[
							'common.ext.file.image'=>[
								'behaviorName'=>'previewImageBehavior',
								'width'=>120,
								'height'=>120
						]],
						'headerHtmlOptions'=>['style'=>'width:15%'],
					],*/
					'title'=>[
						'name'=>'title',
						'header'=>$t('crud.index.gridView.columns.title.header'),
						'type'=>'raw',
						'value'=>'CHtml::link($data->title,["/cp/crud/update", "cid"=>"'.$cid.'", "id"=>$data->id])."<small>"'
							// . '. ("<br/><span>'.$t('label.info.preview_text').':</span> ".(\common\components\helpers\HHtml::intro($data->preview_text)?:"'.$t('label.info.preview_text.emptyText').'"))'
							. '. "</small>"'
					],
					'price'=>[
						'name'=>'price',
						'type'=>'raw',
						'header'=>$t('crud.index.gridView.columns.price.header'),
						'headerHtmlOptions'=>['style'=>'width:15%'],
						'htmlOptions'=>['style'=>'text-align:right'],
						'value'=>'$data->price . "'.$t('price.unit').'"'
					],
 					'active'=>[
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
							'headerHtmlOptions'=>['style'=>'width:10%']
						]
					]						
				]
			]
		],
		'create'=>[
			'url'=>'/cp/crud/create',
			'title'=>$t('page.create.title')
		],
		'update'=>[
			'url'=>'/cp/crud/update',
			'title'=>$t('page.update.title')
		],
		'delete'=>[
			'url'=>'/cp/crud/delete'
		],
		'tabs'=>[
			'main'=>[
				'title'=>$t('tabs.main.title'),
				'attributes'=>[
					'active'=>'checkbox',
					'title',
					'price'=>[
						'type'=>'number',
						'params'=>[
							'htmlOptions'=>['class'=>'form-control w25 inline'],
							'unit'=>$t('price.unit'),
						]
					]
					/*'preview_image'=>[
						'type'=>'common.ext.file.image',
						'behaviorName'=>'previewImageBehavior',
						'params'=>[
							'tagOptions'=>['class'=>'col-xs-12 panel panel-default'],
						]
					],
					'preview_text'=>['type'=>'tinyMce', 'params'=>['full'=>false]],*/
				]
			],
		]
	]
];