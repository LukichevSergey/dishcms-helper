<?php
/**
 * Файл настроек модели \banks\models\Bank
 */
use common\components\helpers\HYii as Y;

$t=Y::ct('\extend\modules\banks\BanksModule.crud', 'banks');
return [
	'class'=>'\extend\modules\banks\models\Bank',
	'menu'=>[
		'backend'=>['label'=>$t('backend.menu.item.label')]
	],
	'buttons'=>[
		'create'=>['label'=>$t('button.create')],
		'settings'=>['label'=>$t('button.settings')]
	],
	'settings'=>[
		'banks'=>[
			'class'=>'\extend\modules\banks\models\BankSettings',
			'title'=>$t('settings.title'),
			// 'menuItemLabel'=>$t('settings.menuItemLabel'),
			'breadcrumbs'=>[$t('page.index.title')=>['/cp/crud/index', 'cid'=>'bank']],
			'viewForm'=>'extend.modules.banks.views.crud._bank_settings_tabs'
		]
	],
	'crud'=>[
		'form'=>[
			'htmlOptions'=>['enctype'=>'multipart/form-data']
		],
		'index'=>[
			'url'=>'/cp/crud/index',
			'title'=>$t('page.index.title'),
			// настройки для виджета zii.widgets.grid.CGridView 
			'gridView'=>[ 
				'dataProvider'=>[
					'criteria'=>[
						'select'=>'`t`.`id`, `t`.`title`, `t`.`active`, `t`.`logo`, `t`.`bank_rate`, `t`.`down_payment`, `t`.`term_loan`, `t`.`decrease`'
					]
				],
				'columns'=>[
					[
						'name'=>'id',
						'headerHtmlOptions'=>['style'=>'width:5%'],
					],
					[
						'name'=>'logo',
						'type'=>[
							'common.ext.file.image'=>[
								'behaviorName'=>'logoBehavior',
								'width'=>120,
								'height'=>120
						]],
						'headerHtmlOptions'=>['style'=>'width:15%'],
					],
					[
						'name'=>'title',
						'header'=>$t('crud.index.gridView.columns.title.header'),
						'type'=>'raw',
						'value'=>'"<strong>".CHtml::link($data->title,["/cp/crud/update", "cid"=>"bank", "id"=>$data->id])."</strong><small>"'
							. '. ("<br/><span>'. $t('label.bank_rate').':</span> ".($data->bank_rate?:"'.$t('emptyValue').'"))'
							. '. ("<br/><span>'. $t('label.down_payment').':</span> ".($data->down_payment?:"0"))'
							. '. ("<br/><span>'. $t('label.term_loan').':</span> ".($data->term_loan?:"'.$t('emptyValue').'"))'
							. '. ("<br/><span>'. $t('label.decrease').':</span> ".($data->decrease?:"'.$t('emptyValue').'"))'
							. '. "</small>"'
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
					'crud.buttons'						
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
			'url'=>'/cp/crud/delete',
			// @todo дополнительные обработчики (callable) перед и после удаления. 
			'before'=>[],
			'after'=>[]
		],
		'tabs'=>[
			'main'=>[
				'title'=>$t('tabs.main.title'),
				// 'view'=>'mymodule.modules.admin.views.crud.mymodel._tab_main',
				// также может быть передано как "view", так и "attributes",
				'attributes'=>[
					'active'=>'checkbox',
					'title',
					//'alias'=>'alias',
					'bank_rate'=>['type'=>'text', 'params'=>[
						'tagOptions'=>['class'=>'row col-xs-4', 'style'=>'padding-left:0'],
						'htmlOptions'=>['class'=>'form-control w100']
					]],
					'down_payment'=>['type'=>'text', 'params'=>[
						'tagOptions'=>['class'=>'row col-xs-4'],
						'htmlOptions'=>['class'=>'form-control w100']
					]],
					'term_loan'=>['type'=>'text', 'params'=>[
						'tagOptions'=>['class'=>'row col-xs-4'],
						'htmlOptions'=>['class'=>'form-control w100']
					]],
					'decrease'=>['type'=>'text', 'params'=>[
						'htmlOptions'=>['class'=>'form-control w25']
					]],
					'logo'=>[
						'type'=>'common.ext.file.image',
						'behaviorName'=>'logoBehavior',
						'params'=>[
							'tagOptions'=>['class'=>'col-xs-12 panel panel-default']
						]
					],
					'preview_text'=>['type'=>'tinyMce', 'params'=>['full'=>false]],
					//'detail_text'=>'tinyMce',
				]
			],
			/*'seo'=>[
				'title'=>$t('tabs.seo.title'),
				// в "use" может быть передан массив [<путь к файлу конфигурации>, <путь к массиву конфигурации>]
				// <путь к массиву конфигурации> - не обязателен, по умолчанию 'crud.form'
				//'use'=>['application.config.crud.seo', 'crud.tabs.main']
				'attributes'=>[
					'meta_h1',
					'meta_title',
					'meta_key',
					'meta_desc'=>'textarea'
				]
			]*/
		]
	]
];