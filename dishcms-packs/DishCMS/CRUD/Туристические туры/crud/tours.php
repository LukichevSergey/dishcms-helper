<?php
/**
 * Туры
 * Добавить в маршруты ['class'=>'\crud\components\rules\PublicRule'],
 */
use common\components\helpers\HYii as Y;

return [
    'class'=>'\crud\models\ar\Tour',
    'access'=>[
        ['allow', 'users'=>['@'], 'roles'=>['admin', 'sadmin', 'crud_tours_manager']],
        ['deny', 'users'=>['*']],
    ],
    'config'=>[
        'tablename'=>'crud_tours',
        'definitions'=>[
            'column.pk',
            'column.create_time'=>['label'=>'Дата создания'],
            'column.update_time',
            'column.published',
            'column.title',
            'column.sef',
            'column.sort',
            ['name'=>'banner', 'type'=>'column.image', 'label'=>'Баннер на странице подробной информации о туре', 'behaviorName'=>'bannerBehavior'],
            ['name'=>'about_text', 'type'=>'column.text', 'label'=>'О туре'],
            ['name'=>'brings_text', 'type'=>'column.text', 'label'=>'Что взять с собой?'],
            ['name'=>'howgetus_text', 'type'=>'column.text', 'label'=>'Как добраться до нас?'],
            ['name'=>'card_image_1', 'type'=>'column.image', 'label'=>'Изображение на переднем плане', 'behaviorName'=>'cardImage1Behavior'],
            ['name'=>'card_image_2', 'type'=>'column.image', 'label'=>'Изображение на заднем плане', 'behaviorName'=>'cardImage2Behavior'],
            'column.price'=>['label'=>'Стоимость тура'],
            'on_index_page'=>['type'=>'TINYINT(1) NOT NULL DEFAULT 0', 'label'=>'Отображать на главной'],
            'duration'=>['type'=>'INT(11) NOT NULL DEFAULT 0', 'label'=>'Количество дней (продолжительность)'],
        ],
        'behaviors'=>[
            'seoBehavior'=>'\seo\behaviors\SeoBehavior',  
            'routePointBehavior'=>[
            	'class'=>'\common\ext\dataAttribute\behaviors\DataAttributeBehavior',
	            'attribute'=>'route_points',
    	        'attributeLabel'=>'Примерная нитка маршрута'
    	    ],
    	    'startDateBehavior'=>[
            	'class'=>'\common\ext\dataAttribute\behaviors\DataAttributeBehavior',
	            'attribute'=>'start_dates',
    	        'attributeLabel'=>'Даты начала тура'
    	    ],          
        ],
        'consts'=>[
            'ROLE_MANAGER'=>'crud_tour_manager',
        ],
        'rules'=>[
            'safe',
            ['title', 'required'],
            ['duration, on_index_page', 'safe'],
            ['on_index_page', 'boolean'],
            ['duration', 'numerical', 'integerOnly'=>true],
            ['title, sef', 'length', 'max'=>255]
        ],
        'scopes'=>[
            'isOnIndexPage'=>[
	            'select'=>'`t`.`id`, `t`.`title`, `t`.`duration`, `t`.`card_image_1`, `t`.`card_image_2`',
            	'condition'=>'`t`.`on_index_page`=1',
            	'order'=>'`t`.`sort`, `t`.`create_time` DESC, `t`.`id` DESC'
            ]
        ],
        'methods'=>[
            'public static function getIndexPageTours() {
                return static::model()->isOnIndexPage()->published()->findAll();
            }',
            'public function getTmbWidth(){
                return 350;
            }',
            'public function getTmbHeight(){
                return 220;
            }',
            'public function getPageUrl() {
                return \crud\components\helpers\HCrudPublic::getViewUrl("tours", $this->id);
            }',
            'public function getDurationLabel() {
            	if((int)$this->duration) { return (int)$this->duration . " " . \common\components\helpers\HTools::pluralLabel((int)$this->duration, ["день", "дня", "дней"]); }
            	else { return ""; }
            }',
            'public function getPhotos() {
            	return \CImage::model()->resetScope()->findAllByAttributes(["model"=>"crud_models_ar_tourphoto", "item_id"=>$this->id], ["order"=>"ordering"]);
            }',
        ]
    ],
    'public'=>[
        'access'=>[
            ['allow', 'users'=>['*'], 'actions'=>['index', 'view']],
        ],
        'routes'=>[
            'index'=>'tours',
            'view'=>'tours/<sef>'
        ],
        'view'=>[
        	'layout'=>'//layouts/other1',
            'view'=>'//crud/tour_view'
        ]
    ],
    'menu'=>[
        'backend'=>['label'=>'Туры']
    ],
    'buttons'=>[
        'create'=>['label'=>'Добавить тур'],
    ],
    'crud'=>[
        'index'=>[
            'url'=>'/cp/crud/index',
            'title'=>'Туры',
            'gridView'=>[
                'id'=>'toursGridViewId',
                'dataProvider'=>[
                    'criteria'=>[
                        'select'=>'`t`.`id`, `t`.`banner`, `t`.`published`, `t`.`sort`, `t`.`title`, `t`.`on_index_page`, `t`.`price`, `t`.`duration`, `t`.`start_dates`'
                    ],
                    'sort'=>['defaultOrder'=>'`t`.`sort`, `t`.`create_time` DESC, `t`.`id` DESC'],
                ],
                'columns'=>[
                    'column.id',
                    [
                        'name'=>'banner',
                        'header'=>'Баннер',
                        'type'=>[
                            'common.ext.file.image'=>[
                                'behaviorName'=>'bannerBehavior',
                                'width'=>120,
                                'height'=>120
                        ]],
                        'headerHtmlOptions'=>['style'=>'width:15%'],                        
                    ],
                    [
                        'type'=>'column.title',
                        'header'=>'Наименование тура',
                        'headerHtmlOptions'=>['style'=>'width:70%;'],
                        'info'=>[
                        	'Продолжительность'=>'$data->getDurationLabel()',
                        	'Стоимость тура'=>'\common\components\helpers\HHtml::price($data->price) . " руб."',
                        	'Даты туров'=>'call_user_func(function()use($data){$list=$data->startDateBehavior->get(true);return implode(", ", array_column($list,"date"));})'
                        ]
                    ],
                    [
                        'name'=>'on_index_page',
                        'type'=>'raw',
                        'header'=>'На главной',
                        'headerHtmlOptions'=>['style'=>'width:5%;text-align:center;white-space:nowrap;'],
                        'htmlOptions'=>['style'=>'width:5%;text-align:center;white-space:nowrap;'],
                        'value'=>function($data) {
                        	if($data->on_index_page) { return \CHtml::tag('span', ['class'=>'label label-success'], 'Да'); }
                        }
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
            'title'=>'Новый тур',
        ],
        'update'=>[
            'url'=>['/cp/crud/update'],
            'title'=>'Редактирование тура',
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
                	'code.html.css'=>function() { Y::css('tours_tabs', '.ui-tabs .ui-tabs-nav li{font-size:12px !important;}'); },
                    'published'=>'checkbox',
                    'sort'=>[
                        'type'=>'number',
                        'params'=>['htmlOptions'=>['class'=>'form-control w10']]
                    ],
                    'title',
                    'sef'=>'alias',
                    'duration'=>[
                        'type'=>'number',
                        'params'=>['htmlOptions'=>['class'=>'form-control w10 inline'], 'unit'=>' дней']
                    ],
                    'price'=>[
                        'type'=>'number',
                        'params'=>['htmlOptions'=>['class'=>'form-control w25 inline'], 'unit'=>' руб.']
                    ], 
                    'start_dates'=>[
            			'type'=>'common.ext.data',
            			'behaviorName'=>'startDateBehavior',
            			'params'=>[
            				'header'=>['date'=>'Дата начала тура'],
            				'types'=>['date'=>'date']
                        ]
            		]                                               
                ]
            ],
            'seo'=>[
                'title'=>'SEO',
                'use'=>['seo.config.crud.seo', 'crud.form']
            ],
            'index_page'=>[
            	'title'=>'Блок на главной',
            	'attributes'=>[
	            	'on_index_page'=>'checkbox',
            		'card_image_1'=>[
                        'type'=>'common.ext.file.image',
                    	'behaviorName'=>'cardImage1Behavior',
                        'params'=>[
                            'tmbWidth'=>420,
                            'tmbHeight'=>280
                        ]
                    ],
                    'card_image_2'=>[
                        'type'=>'common.ext.file.image',
                    	'behaviorName'=>'cardImage2Behavior',
                        'params'=>[
                            'tmbWidth'=>420,
                            'tmbHeight'=>280
                        ]
                    ],
            	]
            ],
            'banner'=>[
            	'title'=>'Баннер',
            	'attributes'=>[
            		'image'=>[
                        'type'=>'common.ext.file.image',
                    	'behaviorName'=>'bannerBehavior',
                        'params'=>[
                            'tmbWidth'=>1920,
                            'tmbHeight'=>430
                        ]
                    ],
            	]
            ],
            'route'=>[
            	'title'=>'Маршрут',
            	'attributes'=>[
            		'route_points'=>[
            			'type'=>'common.ext.data',
            			'behaviorName'=>'routePointBehavior',
            			'params'=>[
            				'header'=>['title'=>'Наименование точки маршрута']
                        ]
            		]
            	]
            ],
            'about'=>[
            	'title'=>'О туре',
            	'attributes'=>[
            		'about_text'=>'tinyMce'
            	]
            ],
            'brings'=>[
            	'title'=>'Что взять с собой?',
            	'attributes'=>[
            		'brings_text'=>'tinyMce'
            	]
            ],
            'howgetus'=>[
            	'title'=>'Как добраться до нас?',
            	'attributes'=>[
            		'howgetus_text'=>'tinyMce'
            	]
            ],
            'photos'=>[
            	'title'=>'Фотографии',
            	'attributes'=>function($model) {
            		if($model->isNewRecord) {
            			$html=\CHtml::tag('div', ['class'=>'alert alert-info'], 'Загрузка фотографий будет доступна после создания тура');
            		}
            		else {
            			$tourPhoto=new \crud\models\ar\TourPhoto;
            			$tourPhoto->id=$model->id;
            			$html=Y::controller()->widget('admin.widget.ajaxUploader.ajaxUploader', [
					        'fieldName'=>'photos',
					        'fieldLabel'=>'Загрузка фото',
						    'model'=>$tourPhoto,
					        'tmb_height'=>100, // 730,
					        'tmb_width'=>100, // 470,
					        'fileType'=>'image'
				    	], true);
            		}
            		return [
            			'code.html.photos'=>$html
        			];
        		}
            ],
        ]
    ]
];
