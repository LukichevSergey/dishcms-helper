<?php
/**
 * Файл настроек модели \extend\modules\buildings\models\Floor
 */
use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HRequest as R;
use extend\modules\buildings\models\Porch;

if(!($porch=Porch::modelById(Y::requestGet('porch')))) $porch=new Porch();
$onBeforeLoad=function() use ($porch) { if(!$porch->id) R::e404(); };

return [
	'class'=>'\extend\modules\buildings\models\Floor',
	'menu'=>[
		'backend'=>['label'=>'Этажи']
	],
	'buttons'=>[
		'create'=>['label'=>'Добавить этаж']
	],	
	'crud'=>[
	    'breadcrumbs'=>[
	        'Планировки'=>\Yii::app()->createUrl("/cp/buildings/index"),
	        'Подъезды'=>\Yii::app()->createUrl("/cp/crud/index", ["cid"=>"buildings_porches"]),
	        $porch->getNumberTitle()
	    ],	    
		'index'=>[
			'onBeforeLoad'=>$onBeforeLoad,
		    'url'=>['/cp/crud/index', 'porch'=>$porch->id],
			'title'=>'Этажи',
			'gridView'=>[ 
				'dataProvider'=>[
					'criteria'=>[
						'select'=>'`t`.`id`, `t`.`title`, `t`.`number`, `t`.`published`, `t`.`image`',
					    'condition'=>'`t`.`porch_id`='.(int)$porch->id,
					    'order'=>'number, title'
					]
				],
			    /*'sortable'=>[
                    'url'=>'/cp/crud/sortableSave',
                    'category'=>'buildings_floors',
				    'key'=>$porch->id
                ],*/
				'columns'=>[
					'id'=>[
						'name'=>'id',
						'header'=>'#',
						'headerHtmlOptions'=>['style'=>'width:5%;text-align:center'],
					],
				    'image'=>[
				        'name'=>'image',
				        'header'=>'Фотография',
				        'type'=>[
				            'common.ext.file.image'=>[
				                'behaviorName'=>'imageBehavior',
				                'width'=>120,
				                'height'=>120
				            ]],
				        'headerHtmlOptions'=>['style'=>'width:15%'],
				    ],				    
				    'number'=>[
				        'name'=>'number',
				        'header'=>'Этаж',
				        'headerHtmlOptions'=>['style'=>'width:40%;text-align:center'],
				        'type'=>'raw',
				        'value'=>'\CHtml::link(
                            $data->getNumberTitle() . ($data->title?"<br/><small>{$data->title}</small>":""),
                            ["/cp/crud/index", "cid"=>"buildings_apartments", "floor"=>$data->id]
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
							'template'=>'{apartments}&nbsp;{update}&nbsp;{delete}',
							'buttons'=>[
							    'apartments' => [
							        'label'=>'<span class="glyphicon glyphicon-home"></span>',
							        'url'=>'\Yii::app()->createUrl("/cp/crud/index", ["cid"=>"buildings_apartments", "floor"=>$data->id])',
							        'options'=>['title'=>'Квартиры', 'class'=>'btn btn-default'],
							    ],
							    'update'=>[
							        'options'=>['class'=>'btn btn-default'],
							    ],
							    'delete'=>[
							        'options'=>['class'=>'btn btn-danger'],
							    ]
							],
							'headerHtmlOptions'=>['style'=>'width:20%'],
						    'htmlOptions'=>['style'=>'text-align:right']
						]
					]					
				]
			]
		],
		'create'=>[
			'onBeforeLoad'=>$onBeforeLoad,
		    'url'=>['/cp/crud/create', 'porch'=>$porch->id],
			'title'=>'Добавление нового этажа',
		    'onAfterSave'=>$onAfterSave
		],
		'update'=>[
			'onBeforeLoad'=>$onBeforeLoad,
		    'url'=>['/cp/crud/update', 'porch'=>$porch->id],
			'title'=>'Редактирование этажа',
		    'onAfterSave'=>$onAfterSave
		],
		'delete'=>[
			'onBeforeLoad'=>$onBeforeLoad,
		    'url'=>['/cp/crud/delete', 'porch'=>$porch->id],
		],
		'form'=>[
		    'htmlOptions'=>['enctype'=>'multipart/form-data'],
		],
	    'tabs'=>[
	        'main'=>[
	            'title'=>'Основные',
	            'attributes'=>[
	                'porch_id'=>[
	                    'type'=>'dropDownList',
	                    'params'=>[
	                        'data'=>$porch->listData(['number'=>function($model, $attribute){
	                        $model->number = $model->getNumberTitle();
	                        }]),
	                        'htmlOptions'=>['class'=>'form-control w50', 'options'=>[$porch->id=>['selected'=>'selected']]]
                        ]
                    ],
                    'published'=>'checkbox',
                    'number'=>[
                        'type'=>'number',
                        'params'=>[
                            'htmlOptions'=>['class'=>'form-control w10']
                        ]
                    ],
                    'title',                    
	            ]
	        ],
	        'text' => [
	            'title' => 'Описание',
	            'attributes'=>[
	                'text'=>['type'=>'tinyMce', 'params'=>['full'=>true]],
	            ]
	        ],
	        'apartments_map' => [
	            'title'=>'Карта квартир',
	            'attributes'=>[
    	            'image'=>[
    	                'type'=>'common.ext.file.image',
    	                'behaviorName'=>'imageBehavior',
    	                'params'=>[
    	                    'tmbWidth'=>700,
    	                    'tmbHeight'=>500
    	                ]
    	            ],
	                'code.html'=>'<div class="alert alert-info">При загрузке новой карты SVG необходимо будет заново привязывать все квартиры данного этажа</div>',
    	            'svg'=>[
    	                'type'=>'common.ext.file.file',
    	                'behaviorName'=>'svgBehavior',
    	                'params'=>[
    	                    'tmbWidth'=>700,
    	                    'tmbHeight'=>500
    	                ]
    	            ],
	           ]
	        ],
	        
	        'map' => [
	            'title'=>'Привязка к карте',
	            'attributes' => [
	                'map_hash'=>'hidden',
	                'code.html' => function($model) {
	                $html = '';
	                if($model->isNewRecord) {
	                    $html = 'Доступно только после создания записи';
	                }
	                else {
	                    $settings = \extend\modules\buildings\components\helpers\HBuildings::settings();
	                    if($settings && $settings->imageBehavior->exists()) {
	                        if($settings->svgBehavior->exists()) {
	                            \common\components\helpers\HYii::module('common')->publishJs(['js/php/utf8_encode.js', 'js/php/md5.js']);
	                            $html .= '<div style="position:relative;min-height:400px">';
	                            $html .= $settings->imageBehavior->img(970, 300, false, ['style'=>'position:absolute;top:0;left:0']);
	                            $html .= '<div class="js-facademap" style="position:absolute;top:0;left:0">';
	                            $html .= file_get_contents($settings->svgBehavior->getFilename(true));
	                            $html .= '</div>';
	                            $html .= '</div>';
	                            $floorsHashes=[];
	                            $floors = $model->findAll(['select'=>'map_hash', 'condition'=>'NOT ISNULL(map_hash) AND (LENGTH(map_hash)>0)']);
	                            if(!empty($floors)) {
	                                foreach($floors as $floor) {
	                                    $floorsHashes[] = $floor->map_hash;
	                                }
	                            }
	                            \common\components\helpers\HYii::js(
	                                false,
	                                ';(function(){
    var $maphash = $("#extend_modules_buildings_models_Floor_map_hash");
    var hashes = '.json_encode($floorsHashes).';
    var pathAll = ".js-facademap svg g";
    var path = ".js-facademap svg g[data-disable!=1]";
    var attr = "id"; 
    var tag = "g";
    hashes.forEach(function(hash) {
        $(pathAll).each(function(){
            if((hash == md5($(this).attr(attr)))) {
                $(this).css("fill", "#a94442");
                $(this).find("rect,path").css("fill", "#a94442");
                $(this).animate({opacity: 0.7},0);
                $(this).attr("data-disable", "1");
            }
        });
    });
    $(pathAll).each(function(){
        if($maphash.val() == md5($(this).attr(attr))) $(this).css("fill", "#069");
        if(!$(this).find(tag).length && $(this).is("[data-disable!=1]")) $(this).animate({opacity: 0.5},0);
    });
    $(path).css("cursor", "pointer");
    $(document).on("mouseover", path, function(e){$(e.target).closest(tag).animate({opacity: 0.9},50);});
    $(document).on("mouseout", path, function(e){$(e.target).closest(tag).animate({opacity: 0.5},50);});
    $(document).on("click", path, function(e){
        $(path).css("fill", "#56c529");
        $maphash.val(md5($(e.target).closest(tag).attr(attr)));
        $(e.target).css("fill", "#069");
    });
})();',
	                                \CClientScript::POS_READY
	                                );
	                        }
	                        else {
	                            $html = 'Не установлено SVG для карты фасада';
	                        }
	                    }
	                    else {
	                        $html = 'Не установлено изображение карты фасада';
	                    }
	                }
	                return $html;
	                }
                ]
            ]
	        
	    ],
	]
];
