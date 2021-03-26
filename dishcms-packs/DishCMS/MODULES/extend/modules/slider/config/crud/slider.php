<?php
/**
 * Файл настроек модели \slider\models\Slider
 */
use common\components\helpers\HYii as Y;
use common\components\helpers\HFile;
use extend\modules\slider\models\Slider;

$t=Y::ct('\extend\modules\slider\SliderModule.crud', 'extend.slider');
return [
	'class'=>'\extend\modules\slider\models\Slider',
	'menu'=>[
		'backend'=>['label'=>$t('slider.backend.menu.item.label')]
	],
	'buttons'=>[
		'create'=>['label'=>$t('slider.button.create')]
	],	
	'crud'=>[		
		'index'=>[
			'url'=>'/cp/crud/index',
			'title'=>$t('slider.page.index.title'),
			'gridView'=>[ 
				'dataProvider'=>[
					'criteria'=>[
						'select'=>'`t`.`id`, `t`.`code`, `t`.`title`, `t`.`active`, `t`.`description`, `t`.`options`'
					]
				],
				'sortable'=>[
                    'url'=>'/cp/crud/sortableSave',
                    'category'=>'slider_sliders'
                ],
				'columns'=>[
					'id'=>[
						'name'=>'id',
						'header'=>'#',
						'headerHtmlOptions'=>['style'=>'width:5%;text-align:center'],
					],
					'code'=>[
						'name'=>'code',
						'headerHtmlOptions'=>['style'=>'width:10%'],
					],
					'title'=>[
						'name'=>'title',
						'header'=>$t('slider.crud.index.gridView.columns.title.header'),
						'type'=>'raw',
						'value'=>'"<strong>".CHtml::link($data->title,["/cp/crud/index", "cid"=>"slide", "slider"=>$data->id])."</strong><small>"'
 							. '. ("<br/><span>'. $t('slider.label.options.widget').':</span> ".(($i=$data->optionsBehavior->find("code","widget"))?(($v=$i["value"])?$v:"Slick"):"Slick"))'
 							. '. ("<br/><span>'. $t('slider.label.options.config').':</span> ".(($i=$data->optionsBehavior->find("code","config"))?(($v=$i["value"])?$v:"default"):"default"))'
 							. '. ("<br/><span>'. $t('slider.label.options.width').':</span> ".(($i=$data->optionsBehavior->find("code","width"))?(($w=$i["value"])?$w."px":"'.$t('emptyValue').'"):"<span class=\'label label-danger\'><i class=\'glyphicon glyphicon-exclamation-sign\'></i> '.$t('emptyValue').'</span>"))'
 							. '. ("<br/><span>'. $t('slider.label.options.height').':</span> ".(($i=$data->optionsBehavior->find("code","height"))?(($h=$i["value"])?$h."px":"'.$t('emptyValue').'"):"<span class=\'label label-danger\'><i class=\'glyphicon glyphicon-exclamation-sign\'></i> '.$t('emptyValue').'</span>"))'
 							. '. ("<br/><span>'. $t('slider.label.options.proportional').':</span> ".(($i=$data->optionsBehavior->find("code","proportional"))?$i["value"]:"yes")."</span>")'
							. '. ("<br/><span>'. $t('slider.label.options.adaptive').':</span> ".(($i=$data->optionsBehavior->find("code","adaptive"))?$i["value"]:"no")."</span>")'
							. '. ("<br/><span>'. $t('slider.label.options.description').':</span> ".(($i=$data->optionsBehavior->find("code","description"))?$i["value"]:"no")."</span>")'
					        . '. ("<br/><span>'. $t('slider.label.options.link').':</span> ".(($i=$data->optionsBehavior->find("code","link"))?$i["value"]:"yes")."</span>")'
 							. '. ("<br/>".($data->description?\common\components\helpers\HHtml::intro($data->description,100):""))'
							. '. "</small>"'
					],
 					'active'=>[
 						'name'=>'active',
 						'header'=>$t('slider.crud.index.gridView.columns.active.header'),
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
							'template'=>'{edit_slides}&nbsp;&nbsp;{update}{delete}',
							'buttons'=>[
								'edit_slides' => [
									'label'=>'<span class="glyphicon glyphicon-picture"></span>',
									'url'=>'\Yii::app()->createUrl("/cp/crud/index", ["cid"=>"slide", "slider"=>$data->id])',
									'options'=>['title'=>$t('slider.crud.index.gridView.columns.buttons.slides')],
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
			'title'=>$t('slider.page.create.title')
		],
		'update'=>[
			'url'=>'/cp/crud/update',
			'title'=>$t('slider.page.update.title'),
		],
		'delete'=>[
			'url'=>'/cp/crud/delete'
		],
		'tabs'=>[
			'main'=>[
				'title'=>$t('slider.tabs.main.title'),
				'attributes'=>[
					'active'=>'checkbox',
					'title',
					'code'=>['type'=>'text', 'params'=>[
						'htmlOptions'=>['class'=>'form-control w25']
					]],
					'description'=>['type'=>'tinyMce', 'params'=>['full'=>false]],
				]
			],
			'slider'=>[
				'title'=>$t('slider.tabs.slider.title'),
				'attributes'=>[
					'options'=>[
						'type'=>'common.ext.data',
						'behaviorName'=>'optionsBehavior',
						'params'=>[
							'wrapperOptions'=>['style'=>'width:100% !important'],
							'header'=>[
								'code'=>['title'=>$t('slider.options.code.title'), 'htmlOptions'=>['style'=>'width:20%']],
								'title'=>['title'=>$t('slider.options.title.title'), 'htmlOptions'=>['style'=>'width:40%']], 
								'value'=>$t('slider.options.value.title'),
								'unit'=>['title'=>$t('slider.options.unit.title')?'':'', 'htmlOptions'=>['style'=>'width:15%']]
							],
							'types'=>['code'=>'default', 'title'=>'default', 'unit'=>'default'],
							'defaultActive'=>true,
							'readOnly'=>['code', 'title', 'unit'],
							'hideActive'=>true,
							'hideAddButton'=>true,
							'hideDeleteButton'=>true,
							'enableSortable'=>false,
							'refreshDefault'=>'code',
							'notes'=>[
								['title'=>$t('slider.label.options.widget.note')], 
								['title'=>call_user_func(function() {
									$cls=uniqid('c');
									$html='<div>';
									$base=dirname(__FILE__).'/../../widgets/configs';
									if($dirs=HFile::getDirs($base)) {
										foreach($dirs as $dir) {
											if($files=HFile::getFiles("{$base}/{$dir}")) {
												$html.="<div class='{$cls}'><div>".ucfirst($dir)."</div><ul>";
												$html.='<li>'.str_replace('.php', '', implode('</li><li>', $files)) . '</li>';
												$html.='</div>';
											}
										}
									}
									$html.='</div>';
									$html.="<style>.{$cls}{font-size:12px;vertical-align:top;border-left:1px solid #ccc;padding:3px;display:inline-block;}";
									$html.=".{$cls}:first-child{border-left:0;}.{$cls} div{font-weight:bold;border-bottom:1px solid #ccc;}";
									$html.=".{$cls} ul{list-style:none;padding-top:5px;}.{$cls} li{line-height:8px;}.{$cls} li:hover{cursor:pointer;text-decoration:underline;}</style>";
									Y::js($cls,'$(document).on("click",".'.$cls.' li",function(e){$("#extend_modules_slider_models_Slider_options_1_value").val($(e.target).text());});',\CClientScript::POS_READY);
									return $html;
								})],
								[],
								[], 
								['title'=>$t('slider.label.options.proportional.note')],
								['title'=>$t('slider.label.options.adaptive.note')],
								['title'=>$t('slider.label.options.description.note')],
								['title'=>$t('slider.label.options.link.note')],
							],
							'default'=>[
								['code'=>'widget', 'title'=>$t('slider.label.options.widget'), 'value'=>'Slick'],
								['code'=>'config', 'title'=>$t('slider.label.options.config'), 'value'=>'default'],
								['code'=>'width', 'title'=>$t('slider.label.options.width'), 'value'=>Slider::WIDTH, 'unit'=>'px'],
								['code'=>'height', 'title'=>$t('slider.label.options.height'), 'value'=>Slider::HEIGHT, 'unit'=>'px'],
								['code'=>'proportional', 'title'=>$t('slider.label.options.proportional'), 'value'=>'yes'],
								['code'=>'adaptive', 'title'=>$t('slider.label.options.adaptive'), 'value'=>'no'],
								['code'=>'description', 'title'=>$t('slider.label.options.description'), 'value'=>'no'],
							    ['code'=>'link', 'title'=>$t('slider.label.options.link'), 'value'=>'yes'],
							],
						]
					],					
				]
			],
			'slides'=>[
			    'title'=>$t('slider.tabs.slides.title'),
			    'attributes'=>[
			        'slide_properties'=>[
			            'type'=>'common.ext.data',
			            'behaviorName'=>'slidePropertiesBehavior',
			            'params'=>[
			                'header'=>[
			                    'code'=>[
			                        'title'=>$t('slider.slideProperties.code.title'),
			                        'htmlOptions'=>['style'=>'width:15%']
			                    ],
			                    'title'=>[
			                        'title'=>$t('slider.slideProperties.title.title'),
			                        'htmlOptions'=>['style'=>'width:20%']
			                    ],
			                    'default'=>$t('slider.slideProperties.default.title'),
			                    'note'=>$t('slider.slideProperties.note.title'),
			                    'unit'=>[
			                        'title'=>$t('slider.slideProperties.unit.title'),
			                        'htmlOptions'=>['style'=>'width:5%;font-size:0.7em;text-align:center']
			                    ]
			                ],
			                'types'=>[
			                    'title'=>['type'=>'text', 'params'=>['htmlOptions'=>['style'=>'min-height:50px']]],
			                    'default'=>['type'=>'text', 'params'=>['htmlOptions'=>['style'=>'min-height:50px']]],
			                    'note'=>['type'=>'text', 'params'=>['htmlOptions'=>['style'=>'min-height:50px;font-size:0.8em;']]],
			                ],
			                'defaultActive'=>true,
			                'default'=>[
			                ]
			            ]
			        ]
		        ]
		    ]
		]
	]
];
