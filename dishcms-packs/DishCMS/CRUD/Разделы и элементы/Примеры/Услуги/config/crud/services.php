<?php
$c=[
    'class'=>'\crud\models\ar\Service',
    'tablename'=>'services',
	'crud_parent'=>'service_sections',
	'crud_parent_class'=>'\crud\models\ar\ServiceSection',
	'public_url_view'=>'/services/view',
    'l'=>[
		'config.section_id'=>'Раздел услуг',
		'config.preview_text'=>'Анонс',
	    'buttons.create'=>'Добавить услугу',
    	'crud.index.title'=>'Услуги',
	    'crud.create.title'=>'Новая услуга',
	    'crud.update.title'=>'Редактирование услуги',
	]
];
return [
    'class'=>$c['class'],
    'relations'=>[
        $c['crud_parent']=>[
            'type'=>'belongs_to',
            'attribute'=>'section_id'
        ],
    ],
    'config'=>[
        'tablename'=>$c['tablename'],
        'definitions'=>[
            'column.pk',
            'foreign.section_id'=>['label'=>$c['l']['config.section_id']],
            'column.title',
            'column.sef',
            'column.image',
            'column.text',
            'column.create_time',
            'column.update_time',
            'column.published',
		    'preview_text'=>['type'=>'TEXT', 'label'=>$c['l']['config.preview_text']]
        ],
        'behaviors'=>[
            'seoBehavior'=>'\seo\behaviors\SeoBehavior',
        ],
        'relations'=>[
            'section'=>[\CActiveRecord::BELONGS_TO, $c['crud_parent_class'], 'section_id']
        ],
        'rules'=>[
            'safe',
	    	['preview_text', 'safe'],
            ['section_id, title, text, sef', 'required'],
        ],        
		'methods'=>[
		    'public function beforeSave() {
                parent::beforeSave();
                $createTime=preg_replace(\'/[^1-9]/\', \'\', $this->create_time);
                if(empty($createTime)) $this->create_time=new \CDbExpression("NOW()");
                return true;
            }',
            'public function getUrl() {
				return \common\components\helpers\HYii::createUrl(\''.$c['public_url_view'].'\', [\'id\'=>$this->id]);
			}',
			'public function getDate() {
				return \common\components\helpers\HYii::param(\'month\') 
					? \common\components\helpers\HYii::formatDateVsRusMonth($this->create_time) 
					: \common\components\helpers\HYii::formatDate($this->create_time, \'dd.MM.yyyy\');
			}'
		]
    ],
    'buttons'=>[
        'create'=>['label'=>$c['l']['buttons.create']],
    ],
    'crud'=>[
        'index'=>[            
            'url'=>'/cp/crud/index',
            'title'=>$c['l']['crud.index.title'],
            'gridView'=>[
                'dataProvider'=>[
                    'criteria'=>[
                        'select'=>'id, title, image, sef, published, create_time, section_id, preview_text'
                    ],
				    'sort'=>['defaultOrder'=>'create_time DESC, id DESC']
                ],
                'columns'=>[
                    'column.id',
                    [
                        'name'=>'image',
                        'type'=>[
                            'common.ext.file.image'=>[
                                'behaviorName'=>'imageBehavior',
                                'width'=>120,
                                'height'=>120
                            ]
                        ],
                        'headerHtmlOptions'=>['style'=>'width:15%']
                    ],
                    [
                        'type'=>'column.title',
                        'info'=>[
                            'Раздел'=>'$data->section->title',
							$c['l']['config.preview_text']=>'$data->preview_text'
                        ]
                    ],
                    [
                        'name'=>'create_time',
                        'header'=>'Дата',
                        'headerHtmlOptions'=>['style'=>'width:15%']
                    ],
                    [
                        'name'=>'published',
                        'header'=>'Опубликовать',
                        'type'=>'common.ext.published'
                    ],
                    'crud.buttons'
                ]
            ]
        ],
        'create'=>[
            'url'=>'/cp/crud/create',
            'title'=>$c['l']['crud.create.title'],
        ],
        'update'=>[
            'url'=>['/cp/crud/update'],
            'title'=>$c['l']['crud.update.title'],
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
        	        'section_id'=>'foreign.dropdownlist',
            	    'title',
                	'sef'=>'alias',
	                'create_time'=>'dateTime',
    	            'image'=>'common.ext.file.image',
					'preview_text'=>'textArea',
            	    'text'=>'tinymce.full'
				]
            ],
			'seo'=>[
				'title'=>'SEO',
				'use'=>['seo.config.crud.seo', 'crud.form']
			]
        ]
    ]
];
