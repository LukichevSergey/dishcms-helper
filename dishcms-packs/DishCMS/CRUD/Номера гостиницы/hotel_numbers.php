<?php
/**
 * Номера отеля
 * Если требуется отображать внутренние страницы, то 
 * добавить в маршруты ['class'=>'\crud\components\rules\PublicRule'],
 */
use common\components\helpers\HYii as Y;

return [
    'class'=>'\crud\models\ar\HotelNumber',
    'access'=>[
        ['allow', 'users'=>['@'], 'roles'=>['admin', 'sadmin']],
        ['deny', 'users'=>['*']],
    ],
    'config'=>[
        'tablename'=>'crud_hotel_numbers',
        'definitions'=>[
            'column.pk',
            'column.create_time'=>['label'=>'Дата создания'],
            'column.update_time',
            'column.published',
            'column.title',
            'column.sort',
            'column.image'=>['label'=>'Основная фотография'],
            'column.text'=>['name'=>'preview_text', 'label'=>'Краткое описание номера']            
        ],
        'behaviors'=>[
            'roomOptionsBehavior'=>[
            	'class'=>'\common\ext\dataAttribute\behaviors\DataAttributeBehavior',
	            'attribute'=>'room_options',
    	        'attributeLabel'=>'В номере'
    	    ],
    	    'hallwayOptionsBehavior'=>[
            	'class'=>'\common\ext\dataAttribute\behaviors\DataAttributeBehavior',
	            'attribute'=>'hallway_options',
    	        'attributeLabel'=>'В коридоре'
    	    ],          
        ],
        'rules'=>[
            'safe',
            ['title', 'required'],
            ['title', 'length', 'max'=>255]
        ],
        'scopes'=>[
            'byDefaultOrder'=>[
	            'order'=>'`t`.`sort`, `t`.`title`, `t`.`id` DESC'
            ]
        ],
        'methods'=>[
            'public function getPhotos() {
                return \CImage::model()->resetScope()->findAllByAttributes(["model"=>"crud_models_ar_hotelnumberphoto", "item_id"=>$this->id], ["order"=>"ordering"]);
            }',
        ]
    ],
    'menu'=>[
        'backend'=>['label'=>'Номера гостиницы']
    ],
    'buttons'=>[
        'create'=>['label'=>'Добавить номер'],
    ],
    'crud'=>[
        'index'=>[
            'url'=>'/cp/crud/index',
            'title'=>'Номера гостиницы',
            'gridView'=>[
                'id'=>'hotelNumbersGridViewId',
                'dataProvider'=>[
                    'sort'=>['defaultOrder'=>'`t`.`sort`, `t`.`title`, `t`.`id` DESC'],
                ],
                'columns'=>[
                    'column.id',
                    [
                        'name'=>'image',
                        'header'=>'Фото',
                        'type'=>[
                            'common.ext.file.image'=>[
                                'behaviorName'=>'imageBehavior',
                                'width'=>120,
                                'height'=>120
                        ]],
                        'headerHtmlOptions'=>['style'=>'width:15%'],                        
                    ],
                    [
                        'type'=>'column.title',
                        'header'=>'Наименование',
                        'headerHtmlOptions'=>['style'=>'width:50%;'],
                        'info'=>[
                        	'Описание'=>'strip_tags($data->preview_text)',
                        	'В номере'=>'call_user_func(function()use($data){$list=$data->roomOptionsBehavior->get(true);return implode(", ", array_column($list,"title"));})',
                        	'В коридоре'=>'call_user_func(function()use($data){$list=$data->hallwayOptionsBehavior->get(true);return implode(", ", array_column($list,"title"));})',
                        	'Дополнительные фото'=>'call_user_func(function() use ($data) {
                                $html="";
                                if($photos=$data->getPhotos()) {
                                    $html.="<br>";
                                    foreach($photos as $photo) {
                                        $html.=\CHtml::image($photo->getTmbUrl(), "", ["style"=>"max-width:80px;max-height:80px;margin-right:5px"]);
                                    }
                                }
                                return $html;
                            })', 
                        ]
                    ],
                    'common.ext.sort',
                    [
                        'name'=>'published',
                        'header'=>'Опубл.',
                        'headerHtmlOptions'=>['style'=>'width:5%;text-align:center;white-space:nowrap;'],
                        'type'=>'common.ext.published'
                    ],
                    'crud.buttons'=>[
                        'type'=>'crud.buttons',
                        'params'=>[
                            'template'=>'{update}{delete}',
                            'buttons'=>[
                                'update'=>[
                                    'label'=>'<span class="glyphicon glyphicon-pencil"></span> Редактировать',
                                    'options'=>['class'=>'btn btn-xs btn-primary w100', 'style'=>'margin-top:2px']
                                ],
                                'delete'=>[
                                    'label'=>'<span class="glyphicon glyphicon-remove"></span> Удалить',
                                    'options'=>['class'=>'btn btn-xs btn-danger w100', 'style'=>'margin-top:2px']
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ],
        'create'=>[
            'scenario'=>'insert',
            'url'=>'/cp/crud/create',
            'title'=>'Новый номер',
        ],
        'update'=>[
            'url'=>['/cp/crud/update'],
            'title'=>'Редактирование номера',
        ],
        'delete'=>[
            'url'=>['/cp/crud/delete'],
        ],
        'form'=>[
            'htmlOptions'=>['enctype'=>'multipart/form-data'],
        ],
        'tabs'=>[
            'main'=>[
                'title'=>'Основные',
                'attributes'=>[
                	'published'=>'checkbox',
                    'sort'=>[
                        'type'=>'number',
                        'params'=>['htmlOptions'=>['class'=>'form-control w10']]
                    ],
                    'title',                    
                    'preview_text'=>'tinyMceLite',
                    'room_options'=>[
            			'type'=>'common.ext.data',
            			'behaviorName'=>'roomOptionsBehavior',
            			'params'=>[
            				'header'=>['title'=>'Наименование'],
                        ]
                    ],
                    'hallway_options'=>[
            			'type'=>'common.ext.data',
            			'behaviorName'=>'hallwayOptionsBehavior',
            			'params'=>[
            				'header'=>['title'=>'Наименование'],
                        ]
            		]                                               
                ]
            ],
            'photos'=>[
            	'title'=>'Фотографии',
            	'attributes'=>function($model) {
            		if($model->isNewRecord) {
            			$html=\CHtml::tag('div', ['class'=>'alert alert-info'], 'Загрузка дополнительных фотографий будет доступна после создания карточки номера');
            		}
            		else {
            			$photo=new \crud\models\ar\HotelNumberPhoto;
                        $photo->id=$model->id;
                        $html='<div class="panel panel-default"><div class="panel-heading">Дополнительные фото</div><div class="panel-body">';
            			$html.=Y::controller()->widget('admin.widget.ajaxUploader.ajaxUploader', [
					        'fieldName'=>'photos',
					        'fieldLabel'=>'Загрузка фото',
						    'model'=>$photo,
					        'tmb_height'=>100, // 730,
					        'tmb_width'=>100, // 470,
					        'fileType'=>'image'
                        ], true);
                        $html.='</div>';
            		}
            		return [
                        'image'=>[
                            'type'=>'common.ext.file.image',
                            'params'=>[
                                'tmbWidth'=>445,
                                'tmbHeight'=>445
                            ]
                        ],
            			'code.html.photos'=>$html
        			];
        		}
            ],
        ]
    ]
];
