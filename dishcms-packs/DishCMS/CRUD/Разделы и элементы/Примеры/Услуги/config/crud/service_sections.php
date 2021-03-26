<?php
$c=[
	'class'=>'\crud\models\ar\ServiceSection',
	'tablename'=>'service_sections',
	'crud_items'=>'services',
	'crud_items_class'=>'\crud\models\ar\Service',
	'public_url_view'=>'/services/section',
	'l'=>[
		'config.preview_text'=>'Краткое описание',
		'menu.backend'=>'Наши услуги',
		'buttons.create'=>'Добавить раздел',
		'crud.index.title'=>'Разделы услуг',
		'crud.create.title'=>'Новый раздел',
		'crud.update.title'=>'Редактирование раздела',
	]
];
return [
    'class'=>$c['class'],
    'relations'=>[ 
        $c['crud_items']=>[
            'type'=>'has_many',
            'attribute'=>'section_id'
        ],
    ],
    'config'=>[
        'tablename'=>$c['tablename'],
        'definitions'=>[
            'column.pk',
            'column.nestedset',
            'column.title',
			'column.text',			
            'column.image',
            'column.sef',
            'column.create_time',
            'column.published',
            'preview_text'=>['type'=>'TEXT', 'label'=>$c['l']['config.preview_text']]
        ],
		'behaviors'=>[
            'seoBehavior'=>'\seo\behaviors\SeoBehavior',
        ],
        'relations'=>[
            'items'=>[\CActiveRecord::HAS_MANY, $c['crud_items_class'], 'section_id']
        ],
        'rules'=>[
            'safe',
            ['preview_text', 'safe'],
            ['title, sef', 'required'],
        ],
        'methods'=>[
			'private static $isUpdateRunned=false;',
			'private static $isDeleteRunned=false;',
			'public function update($attributes) { 
				if(!static::$isUpdateRunned && (count($attributes)===1) && in_array(\'published\', $attributes)) {
					static::$isUpdateRunned=true;
					return $this->save(false, $attributes);
				}
				if(static::$isUpdateRunned) static::$isUpdateRunned=false;
				return parent::update($attributes);
			}',
            'public function save($runValidation=true,$attributes=null) {
                return $this->nestedSetBehavior->save($runValidation, $attributes);
            }',
            'public function delete() {
				if(!static::$isDeleteRunned) { 
					static::$isDeleteRunned=true;
					return $this->nestedSetBehavior->delete();
				}
				if(static::$isDeleteRunned) static::$isDeleteRunned=false;
				return parent::delete();
            }',
            'public function afterDelete() {
				parent::afterDelete();
				$itemsTableName=\crud\components\helpers\HCrud::param(\''.$c['crud_items'].'\', \'config.tablename\');
				$getItemsIdSql=\'SELECT `t1`.`id` FROM `\'.$itemsTableName.\'` AS `t1` LEFT JOIN `'.$c['tablename'].'` AS `t2` ON(`t1`.`section_id`=`t2`.`id`) WHERE ISNULL(`t2`.`id`)\';
				$itemsIds=\common\components\helpers\HDb::queryColumn($getItemsIdSql);
				$criteria=\common\components\helpers\HDb::criteria();
				$criteria->addInCondition(\'id\', $itemsIds);
				if($items='.$c['crud_items_class'].'::model()->findAll($criteria)) {
					foreach($items as $item) { $item->delete(); }
				}
				return true;
			}',
            'public function getChildrenSections($condition=\'\', $params=[]) {
				return $this->children()->published()->findAll($condition, $params);
			}',
            'public function getItemsDataProvider($options=[]) {
				$criteria=\common\components\helpers\HDb::criteria(\common\components\helpers\HArray::get($options, \'criteria\', []));
				$criteria->addColumnCondition([\'section_id\'=>$this->id]);
				$options[\'criteria\']=$criteria;
                return new \CActiveDataProvider(\''.$c['crud_items_class'].'\', $options);
            }',
			'public function getUrl() {
				return \common\components\helpers\HYii::createUrl(\''.$c['public_url_view'].'\', [\'id\'=>$this->id]);
			}'
        ]
    ],
    'menu'=>[
        'backend'=>['label'=>$c['l']['menu.backend']]
    ],
    'buttons'=>[
        'create'=>['label'=>$c['l']['buttons.create']],
    ],
    'crud'=>[
        'index'=>[
            'url'=>'/cp/crud/index',
            'title'=>$c['l']['crud.index.title'],
            'nestedset'=>[
                'dataProvider'=>[
                    'criteria'=>[
                        'select'=>'id, title, image, sef, published, create_time',
                    ]
                ],
                'itemViewData'=>[
                    'columnsOptions'=>[
                        'columnOptions'=>['class'=>'pull-right', 'style'=>'margin-right:10px'],
                    ]
                ],
                'columns'=>[
                    'title.relation.services'=>[
                        'htmlOptions'=>['class'=>'col-md-6']
                    ],
                    'btn.delete',
                    'btn.update',
                    'common.ext.published',
                    'relation.link.'.$c['crud_items']=>['relationCount'=>true],
                ]
            ],
        ],
        'create'=>[
            'url'=>'/cp/crud/create',
            'title'=>$c['l']['crud.create.title'],
        ],
        'update'=>[
            'url'=>'/cp/crud/update',
            'title'=>$c['l']['crud.update.title'],
        ],
        'delete'=>[
            'url'=>'/cp/crud/delete',
        ],
        'form'=>[
            'htmlOptions'=>['enctype'=>'multipart/form-data'],
		],
		'tabs'=>[
            'main'=>[
                'title'=>'Основные',
                'attributes'=>[
					'published'=>'checkbox',
	                'title',
    	            'sef'=>'alias',
        	        'image'=>'common.ext.file.image',
        	        'preview_text'=>'textArea',
            	    'text'=>'tinyMce'
                ]
            ],
            'seo'=>[
                'title'=>'SEO',
                'use'=>['seo.config.crud.seo', 'crud.form']
            ]
        ]
    ]
];
