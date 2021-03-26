<?php
/**
 * Файл настроек модели \PriceSubSection
 */
use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HRequest;

if(!($section=PriceSection::modelById(Y::requestGet('section')))) $section=new PriceSection();
$onBeforeLoad=function() use ($section) { if(!$section->id) HRequest::e404(); };

return [
	'class'=>'\PriceSubSection',
	'menu'=>[
		'backend'=>['label'=>'Прайс-листы', 'disabled'=>true]
	],
	'buttons'=>[
		'create'=>['label'=>'Добавить прайс-лист'],
	],
	'crud'=>[
		'breadcrumbs'=>[
			'Прайс-листы'=>\Yii::app()->createUrl("/cp/crud/index", ["cid"=>"price_section"]),
			$section->title
		],
		'index'=>[
			'onBeforeLoad'=>$onBeforeLoad,
			'url'=>['/cp/crud/index', 'section'=>$section->id],
			'title'=>'Прайс-листы раздела ' . $section->title,
			'titleBreadcrumb'=>'Прайс-листы',
			'gridView'=>[ 
				'dataProvider'=>[
					'criteria'=>[
						'select'=>'`t`.`id`, `t`.`section_id`, `t`.`title`, `t`.`active`',
						'condition'=>'section_id=:sectionId',
						'params'=>[':sectionId'=>$section->id]
					]
				],
				'sortable'=>[
					'url'=>'/cp/crud/sortableSave',
					'category'=>'price_subsections',
					'key'=>$section->id
				],
				'columns'=>[
					[
						'name'=>'id',
						'header'=>'#',
						'headerHtmlOptions'=>['style'=>'width:5%'],
					],
					[
						'name'=>'title',
						'header'=>'Наименование',
						'type'=>'raw',
						'value'=>'"<strong>".CHtml::link($data->title,["/cp/crud/update", "cid"=>"price_subsection", "section"=>$data->section_id, "id"=>$data->id])."</strong>"'
					],
 					[
 						'name'=>'active',
 						'header'=>'Отображать',
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
			'onBeforeLoad'=>$onBeforeLoad,
			'url'=>['/cp/crud/create', 'section'=>$section->id],
			'title'=>'Добавить прайс-лист'
		],
		'update'=>[
			'onBeforeLoad'=>$onBeforeLoad,
			'url'=>['/cp/crud/update', 'section'=>$section->id],
			'title'=>'Редактирование прайс-листа'
		],
		'delete'=>[
			'onBeforeLoad'=>$onBeforeLoad,
			'url'=>['/cp/crud/delete', 'section'=>$section->id]
		],
		'form'=>[
			'attributes'=>[
				'section_id'=>['type'=>'hidden', 'params'=>['htmlOptions'=>['value'=>$section->id]]],
				'active'=>'checkbox',
				'title',
				'text'=>['type'=>'tinyMce', 'params'=>['full'=>true]],
			]
		]
	]
];
