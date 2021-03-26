<?php
/**
 * Файл настроек модели \extend\modules\buildings\models\Apartment
 */
use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HRequest as R;
use extend\modules\buildings\models\Floor;

if(!($floor=Floor::modelById(Y::requestGet('floor')))) $floor=new Floor();
$onBeforeLoad=function() use ($floor) { if(!$floor->id) R::e404(); };

$breadcrumbs = [];
$floorsListData = [];
if($floor->porch) {
    $floors = $floor->porch->getRelated('floors', false, ['select'=>'`id`, `number`', 'order'=>'`number`']);
    if(!empty($floors)) {
        $floorsListData = \CHtml::listData($floors, 'id', function($model){return 'Этаж № ' . $model->number;});
    }
    
    $breadcrumbs= [
        'Планировки'=>\Yii::app()->createUrl("/cp/buildings/index"),
        'Подъезды'=>\Yii::app()->createUrl("/cp/crud/index", ["cid"=>"buildings_porches"]),
        $floor->porch->getNumberTitle() => \Yii::app()->createUrl("/cp/crud/index", ["cid"=>"buildings_floors", "porch"=>$floor->porch->id]),
        $floor->getNumberTitle()
    ];
}

return [
	'class'=>'\extend\modules\buildings\models\Apartment',
	'menu'=>[
		'backend'=>['label'=>'Квартиры']
	],
	'buttons'=>[
		'create'=>['label'=>'Добавить квартиру']
	],	
	'crud'=>[
	    'breadcrumbs'=>$breadcrumbs,	    
		'index'=>[
			'onBeforeLoad'=>$onBeforeLoad,
		    'url'=>['/cp/crud/index', 'floor'=>$floor->id],
			'title'=>'Квартиры',
			'gridView'=>[ 
				'dataProvider'=>[
					'criteria'=>[
						'select'=>'`t`.`id`, `t`.`title`, `t`.`published`, `t`.`sold`, `t`.`image`, `t`.`rooms`, `t`.`area`, `t`.`price`, `t`.`sale_price`',
					    'condition'=>'`t`.`floor_id`='.(int)$floor->id,
					    'order'=>'title, id'
					]
				],
			    /*'sortable'=>[
                    'url'=>'/cp/crud/sortableSave',
                    'category'=>'buildings_floors',
			        'key'=>$floor->porch->id
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
				        'header'=>'Квартира',
				        'headerHtmlOptions'=>['style'=>'width:40%;text-align:center'],
				        'type'=>'raw',
				        'value'=>'\CHtml::link(
                            "Квартира: " . $data->title,
                            ["/cp/crud/update", "cid"=>"buildings_apartments", "id"=>$data->id, "floor"=>'.$floor->id.']
                        )'
				        . '. ($data->rooms ? "<br/><small><b>Кол-во комнат:</b> {$data->rooms}</small>" : "")'
				        . '. ($data->area ? "<br/><small><b>Общая площадь:</b> {$data->area} м<sup>2</sup></small>" : "")'
				        . '. ((float)$data->price ? "<br/><small><b>Цена:</b> ".\common\components\helpers\HHtml::price($data->price)." руб.</small>" : "")'
				        . '. ((float)$data->sale_price ? "<br/><small><b>Цена по акции:</b> ".\common\components\helpers\HHtml::price($data->sale_price)." руб.</small>" : "")'				        
				    ],			
 					'sold'=>[
 						'name'=>'sold',
 						'header'=>'Статус',
 					    'type'=>'raw',
 					    'value'=>'($data->sold ? "<span class=\"label label-success\">продана</span>" : "<span class=\"label label-default\">свободна</span>")',
 						/*'type'=>[
 							'common.ext.active'=>[
 								'behaviorName'=>'soldBehavior',
 							] 
						],*/
 						'headerHtmlOptions'=>['style'=>'width:10%;text-align:center'],
 					    'htmlOptions'=>['style'=>'text-align:center'],
 					],
				    'active'=>[
				        'name'=>'published',
				        'header'=>'Опубликовать',
				        'type'=>[
				            'common.ext.active'=>[
				                'behaviorName'=>'publishedBehavior',
				            ]
				        ],
				        'headerHtmlOptions'=>['style'=>'width:10%']
				    ],
					'crud.buttons'=>[
						'type'=>'crud.buttons',
						'params'=>[
							'template'=>'{update}&nbsp;{delete}',
							'buttons'=>[
							    'update'=>[
							        'options'=>['class'=>'btn btn-default'],
							    ],
							    'delete'=>[
							        'options'=>['class'=>'btn btn-danger'],
							    ]
							],
							'headerHtmlOptions'=>['style'=>'width:15%'],
						    'htmlOptions'=>['style'=>'text-align:right']
						]
					]					
				]
			]
		],
		'create'=>[
			'onBeforeLoad'=>$onBeforeLoad,
		    'url'=>['/cp/crud/create', 'floor'=>$floor->id],
			'title'=>'Добавление новой квартиры'
		],
		'update'=>[
			'onBeforeLoad'=>$onBeforeLoad,
		    'url'=>['/cp/crud/update', 'floor'=>$floor->id],
			'title'=>'Редактирование квартиры'
		],
		'delete'=>[
			'onBeforeLoad'=>$onBeforeLoad,
		    'url'=>['/cp/crud/delete', 'floor'=>$floor->id],
		],
		'form'=>[
		    'htmlOptions'=>['enctype'=>'multipart/form-data'],
		],
	    'tabs'=>[
	        'main'=>[
	            'title'=>'Основные',
    		    'attributes'=>[
    		        'code.html.0'=>function($model){
        		        if(!$model->isNewRecord) {
        		            return \CHtml::tag('div', ['style'=>'width:100%;position:relative'], \CHtml::link(
        		                'посмотреть на сайте', 
        		                ['/buildings/apartment', 'id'=>$model->id], 
        		                ['class'=>'btn btn-default', 'target'=>'_blank', 'style'=>'position:absolute;right:0']
        		            ));
        		        }
    		        },
    		        'sold'=>'checkbox',
    		        'published'=>'checkbox',		        		        		        
    		        'floor_id'=>[
    		            'type'=>'dropDownList', 
    		            'params'=>[
    		                'data'=>$floorsListData,
    		                'htmlOptions'=>['class'=>'form-control w50', 'options'=>[$floor->id=>['selected'=>'selected']]],
    		            ]
    		        ],
    		        'code.html.floor_id' => '<div style="font-size:13px;margin-bottom:10px;margin-top:-15px">Привязка к карте будет доступна для нового номера подъеза, только после сохранения</div>',
    		        'title',
    		        'area'=>[
    		            'type'=>'number',
    		            'params'=>[
    		                'htmlOptions'=>['class'=>'form-control inline', 'style'=>'width:20%', 'step'=>0.01],
    		                'unit'=>'&nbsp;м<sup>2</sup>.'
    		            ]
    		        ],
    		        'rooms'=>[
    		            'type'=>'number',
    		            'params'=>[
    		                'htmlOptions'=>['class'=>'form-control inline', 'style'=>'width:20%'],
    		            ]
    		        ],
    		        'code.html.1'=>'<div class="row">',
    		        'price'=>[
    		            'type'=>'number',
    		            'params'=>[
    		                'tagOptions'=>['class'=>'col-md-4', 'style'=>'padding-left:0'],
    		                'htmlOptions'=>['class'=>'form-control inline', 'style'=>'width:75%'],
    		                'unit'=>'&nbsp;руб.'
    		            ]
    		        ],
    		        'sale_price'=>[
    		            'type'=>'number',
    		            'params'=>[
    		                'tagOptions'=>['class'=>'col-md-4'],
    		                'htmlOptions'=>['class'=>'form-control inline', 'style'=>'width:75%'],
    		                'unit'=>'&nbsp;руб.'
    		            ]
    		        ],
    		        'code.html.2'=>'</div>',
    		        'image'=>[
    		            'type'=>'common.ext.file.image',
    		            'behaviorName'=>'imageBehavior'
    		        ],
    		        'props'=>[
    		            'type'=>'common.ext.data',
    		            'behaviorName'=>'propsBehavior',
    		            'params'=>[
    		                //'wrapperOptions'=>['style'=>'width:50% !important'],
    		                'header'=>[
    		                    'title'=>['title'=>'Наименование', 'htmlOptions'=>['style'=>'width:50%']],
    		                    'value'=>['title'=>'Значение', 'htmlOptions'=>['style'=>'width:20%']],
    		                    'unit'=>['title'=>'Ед.изм.', 'htmlOptions'=>['style'=>'width:10%']],
    		                ],
    		                'defaultActive'=>true,
    		                'enableSortable'=>true,
    		                'default'=>[
    		                    ['title'=>'', 'value'=>'', 'unit'=>'']
    		                ],
    		            ]
    		        ],    		        
    		    ]
    		],
    		'text' => [
    		    'title' => 'Описание',
    		    'attributes'=>[
    		        'text'=>['type'=>'tinyMce', 'params'=>['full'=>true]],
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
    		                if($model->floor && $model->floor->imageBehavior->exists()) {
    		                    if($model->floor->svgBehavior->exists()) {
    		                        \common\components\helpers\HYii::module('common')->publishJs(['js/php/utf8_encode.js', 'js/php/md5.js']);
            		                $html .= '<div style="position:relative;min-height:600px">';
            		                $html .= $model->floor->imageBehavior->img(700, 500, false, ['style'=>'position:absolute;top:0;left:0']);
            		                $html .= '<div class="js-floormap" style="position:absolute;top:0;left:0">';
            		                $html .= file_get_contents($model->floor->svgBehavior->getFilename(true)); //'<img style="position:absolute;top:0;left:0;opacity:0.4" src="' . $model->floor->svgBehavior->getSrc() . '" />';
            		                $html .= '</div>';
            		                $html .= '</div>';
            		                $apartmentsHashes=[];
            		                $apartments = $model->floor->getRelated('apartments', true, ['select'=>'map_hash', 'condition'=>'NOT ISNULL(map_hash) AND (LENGTH(map_hash)>0)']);
            		                if(!empty($apartments)) {
            		                    foreach($apartments as $apartment) {
            		                        $apartmentsHashes[] = $apartment->map_hash;
            		                    }
            		                }
            		                \common\components\helpers\HYii::js(
            		                    false, 
';(function(){
    var $maphash = $("#extend_modules_buildings_models_Apartment_map_hash");
    var hashes = '.json_encode($apartmentsHashes).';
    var pathAll = ".js-floormap svg path";
    var path = ".js-floormap svg path[data-disable!=1]";
    var attr = "d"; 
    var tag = "path";
    hashes.forEach(function(hash) { 
        $(pathAll).each(function(){
            if((hash == md5($(this).attr(attr)))) {
                $(this).css("fill", "#a94442");
                $(this).attr("data-disable", "1");
            }
        });
    });        
    $(pathAll).each(function(){if($maphash.val() == md5($(this).attr(attr))) $(this).css("fill", "#069");});
    $(pathAll).animate({opacity: 0.4},50);
    $(path).css("cursor", "pointer");
    $(document).on("mouseover", path, function(e){$(e.target).closest(tag).animate({opacity: 0.6},50);});
    $(document).on("mouseout", path, function(e){$(e.target).closest(tag).animate({opacity: 0.4},50);});
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
    		                        $html = 'Не установлено SVG для карты квартир';
    		                    }
        		            }
        		            else {
        		                $html = 'Не установлено изображение карты квартир';
        		            }
    		            }
    		            return $html;
    		        }
    		    ]
    		]
    	]
	]
];
