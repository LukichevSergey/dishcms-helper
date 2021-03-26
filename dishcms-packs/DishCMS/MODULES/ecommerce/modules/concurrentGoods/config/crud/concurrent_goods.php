<?php
/**
 * Файл настроек модели \extend\modules\pages\models\Page
 */
use common\components\helpers\HYii as Y;

/** @var string $cid индетификатор настроек CRUD для модели. */
$cid=Y::requestGet('cid', 'ecommerce_concurrent_goods');

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
						'select'=>'`t`.`id`, `t`.`title`, `t`.`preview_text`, `t`.`published`, `t`.`update_time`, `t`.`preview_image`, `t`.`preview_image_enable`',
					]
				],
				'columns'=>[
					'id'=>[
						'name'=>'id',
						'header'=>'#',
						'headerHtmlOptions'=>['style'=>'width:5%'],
					],
					'preview_image'=>[
						'name'=>'preview_image',
						'header'=>$t('crud.index.gridView.columns.preview_image.header'),
						'type'=>[
							'common.ext.file.image'=>[
								'behaviorName'=>'previewImageBehavior',
								'width'=>120,
								'height'=>120
						]],
						'headerHtmlOptions'=>['style'=>'width:15%'],
					],
					'title'=>[
						'name'=>'title',
						'header'=>$t('crud.index.gridView.columns.title.header'),
						'type'=>'raw',
						'value'=>'"<strong>".CHtml::link($data->title,["/cp/crud/update", "cid"=>"'.$cid.'", "id"=>$data->id])."</strong><small>"'
							. '. ("<br/><span>'.$t('label.info.create_time').':</span> ".date_format(new \DateTime($data->create_time), "d.m.Y"))'
							. '. ("<br/><span>'.$t('label.info.update_time').':</span> ".date_format(new \DateTime($data->update_time), "d.m.Y H:i"))'
							. '. ("<br/><span>'.$t('label.info.preview_text').':</span> ".(\common\components\helpers\HHtml::intro($data->preview_text)?:"'.$t('label.info.preview_text.emptyText').'"))'
							. '. "</small>"'
					],
 					'published'=>[
 						'name'=>'published',
 						'header'=>$t('crud.index.gridView.columns.published.header'),
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
							'template'=>'{preview}&nbsp;&nbsp;{update}{delete}',
							'buttons'=>[
								'preview' => [
									'label'=>'<span class="glyphicon glyphicon-file"></span>',
									'url'=>'\Yii::app()->createUrl("/crud/default/view", ["cid"=>"'.$cid.'", "id"=>$data->id])',
									'options'=>['title'=>$t('crud.index.gridView.columns.buttons.preview'), 'target'=>'_blank'],
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
					'published'=>'checkbox',
					'title',
					'sef'=>'alias',
					'create_time'=>'date',
					'preview_image'=>[
						'type'=>'common.ext.file.image',
						'behaviorName'=>'previewImageBehavior',
						'params'=>[
							'tagOptions'=>['class'=>'col-xs-12 panel panel-default'],
						]
					],
					'preview_text'=>['type'=>'tinyMce', 'params'=>['full'=>false]],
					'detail_text'=>'tinyMce',
				]
			],
			'seo'=>[
				'title'=>$t('tabs.seo.title'),
				'use'=>['extend.modules.seo.config.crud.seo', 'crud.form']
			]
		]
	]
];