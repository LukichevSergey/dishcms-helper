<?php
/**
 * Файл настроек модели \slider\models\Slide
 */
use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HRequest;
use extend\modules\slider\models\Slider;

if(!($slider=Slider::modelById(Y::requestGet('slider')))) $slider=new Slider();
$onBeforeLoad=function() use ($slider) { if(!$slider->id) HRequest::e404(); };

$slideOptions=['default'=>[],'notes'=>[]];
if($slider && !$slider->isNewRecord) {
	foreach($slider->slidePropertiesBehavior->get(true) as $optionData) {
		if($code=A::get($optionData, 'code')) {
 			$slideOptions['default'][]=[
 				'code'=>$code,
 				'title'=>A::get($optionData, 'title', $code),
 				'value'=>A::get($optionData, 'default'),
 				'unit'=>A::get($optionData, 'unit')
 			];
 			$slideOptions['notes'][]=['title'=>A::get($optionData, 'note')];
 		}
	}
}

$t=Y::ct('\extend\modules\slider\SliderModule.crud', 'extend.slider');
return [
	'class'=>'\extend\modules\slider\models\Slide',
	'menu'=>[
		'backend'=>['label'=>$t('slide.backend.menu.item.label'), 'disabled'=>true]
	],
	'buttons'=>[
		'create'=>['label'=>$t('slide.button.create')],
	],
	'crud'=>[
		'form'=>[
			'htmlOptions'=>['enctype'=>'multipart/form-data']
		],
		'breadcrumbs'=>[
			$t('slider.page.index.title')=>\Yii::app()->createUrl("/cp/crud/index", ["cid"=>"slider"]),
			$slider->title
		],
		'index'=>[
			'onBeforeLoad'=>$onBeforeLoad,
			'url'=>['/cp/crud/index', 'slider'=>$slider->id],
			'title'=>$t('slide.page.index.title', ['{slider}'=>$slider->title]),
			'titleBreadcrumb'=>$t('slide.page.index.title.breadcrumb'),
			'gridView'=>[ 
				'dataProvider'=>[
					'criteria'=>[
						'select'=>'`t`.`id`, `t`.`slider_id`, `t`.`title`, `t`.`active`, `t`.`image`, `t`.`description`, `t`.`options`',
						'condition'=>'slider_id=:sliderId',
						'params'=>[':sliderId'=>$slider->id]
					]
				],
				'sortable'=>[
					'url'=>'/cp/crud/sortableSave',
					'category'=>'slider_slides',
					'key'=>$slider->id
				],
				'columns'=>[
					[
						'name'=>'id',
						'header'=>'#',
						'headerHtmlOptions'=>['style'=>'width:5%'],
					],
					[
						'name'=>'image',
						'type'=>[
							'common.ext.file.image'=>[
								'behaviorName'=>'imageBehavior',
								'width'=>120,
								'height'=>120
						]],
						'headerHtmlOptions'=>['style'=>'width:15%'],
					],
					[
						'name'=>'title',
						'header'=>$t('slide.crud.index.gridView.columns.title.header'),
						'type'=>'raw',
						'value'=>'"<strong>".CHtml::link($data->title,["/cp/crud/update", "cid"=>"slide", "slider"=>$data->slider_id, "id"=>$data->id])."</strong><small>"'
							. '. "</small>"'
					],
 					[
 						'name'=>'active',
 						'header'=>$t('slide.crud.index.gridView.columns.active.header'),
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
			'url'=>['/cp/crud/create', 'slider'=>$slider->id],
			'title'=>$t('slide.page.create.title')
		],
		'update'=>[
			'onBeforeLoad'=>$onBeforeLoad,
			'url'=>['/cp/crud/update', 'slider'=>$slider->id],
			'title'=>$t('slide.page.update.title')
		],
		'delete'=>[
			'onBeforeLoad'=>$onBeforeLoad,
			'url'=>['/cp/crud/delete', 'slider'=>$slider->id]
		],
	    'tabs'=>function() use ($t, $slider, $slideOptions) {
	        $tabs=[
    			'main'=>[
    				'title'=>$t('slide.tabs.main.title'),
    				'attributes'=>function($model) use ($slider) {
						$attributes=[
	    					'slider_id'=>['type'=>'hidden', 'params'=>['htmlOptions'=>['value'=>$slider->id]]],
    						'active'=>'checkbox',
    						'title'
						];
						
						if($slider->isOptionYes('link')) {
						    $attributes[]='url';
						}
						
						$attributes['image']=[
							'type'=>'common.ext.file.image',
							'behaviorName'=>'imageBehavior',
							'params'=>[
								'tmbWidth'=>$slider->getOption('width')?:Slider::WIDTH,
								'tmbHeight'=>$slider->getOption('height')?:Slider::HEIGHT,
							    'tmbProportional'=>$slider->isOptionYes('proportional', true),
							    'tmbAdaptive'=>$slider->isOptionYes('adaptive')
							]
						];

						if($slider->isOptionYes('description')) {
							$attributes['description']=['type'=>'tinyMce', 'params'=>['full'=>false]];
						}

						return $attributes;
					}
    			]
	        ];
	        if(!empty($slideOptions['default'])) {
	            $tabs['options']=[
				    'title'=>$t('slide.tabs.options.title'),
			        'attributes'=>[
    			        'options'=>[
    				    	'type'=>'common.ext.data',
    						'behaviorName'=>'optionsBehavior',
    						'params'=>[
    							//'wrapperOptions'=>['style'=>'width:50% !important'],
    							'header'=>[
    								'code'=>['title'=>$t('slide.options.code.title'), 'htmlOptions'=>['style'=>'width:15%']],
    								'title'=>['title'=>$t('slide.options.title.title'), 'htmlOptions'=>['style'=>'width:20%']], 
    								'value'=>$t('slide.options.value.title'),
    								'unit'=>['title'=>$t('slide.options.unit.title')?'':'', 'htmlOptions'=>['style'=>'width:5%']]
    							],
    							'types'=>['code'=>'default', 'title'=>'default', 'unit'=>'default'],
    							'notes'=>$slideOptions['notes'],
    							'defaultActive'=>true,
    							'refreshDefault'=>'code',
    							'refreshDefaultSafe'=>false,
    							'readOnly'=>['code', 'title', 'unit'],
    							'hideActive'=>true,
    							'hideAddButton'=>true,
    							'hideDeleteButton'=>true,
    							'enableSortable'=>false,
    							'default'=>$slideOptions['default'],
    						]
    					]
			        ]
                ];
	        }
	        
			return $tabs;
	    }
	]
];
