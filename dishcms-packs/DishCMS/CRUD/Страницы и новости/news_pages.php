<?php
/**
 * Акции
 * Добавить в маршруты ['class'=>'\crud\components\rules\PublicRule'],
 */
use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HDb;

$cid="news";
$t=[
    'attribute.sort'=>'Сортировка',
    'attribute.preview_text'=>'Анонс',
    'attribute.create_time'=>'Дата',
    'menu.backend.label'=>'Новости',
    'buttons.create.label'=>'Добавить новость',
    'settings.title'=>'Настройки страницы новостей',
    'settings.attributes.page_size'=>'Кол-во новостей на странице',
    'settings.attributes.meta_h1'=>'H1',
    'settings.attributes.meta_title'=>'Заголовок браузера',
    'settings.attributes.meta_key'=>'META "keywords"',
    'settings.attributes.meta_desc'=>'META "description"',
    'settings.attributes.text'=>'Текст',
    'settings.tabs.main.title'=>'Основные',
    'settings.tabs.seo.title'=>'SEO',
    'crud.index.title'=>'Новости',
    'crud.index.gridView.columns.title.header'=>'Новость',
    'crud.index.gridView.columns.title.info.link'=>'Посмотреть на сайте',
    'crud.index.gridView.columns.title.info.link.title'=>'перейти',
    'crud.index.gridView.columns.title.info.preview_text'=>'Анонс',
    'tabs.main.title'=>'Основные',
    'tabs.seo.title'=>'SEO',
    'crud.create.title'=>'Добавление новости',
    'crud.update.title'=>'Редактирование новости',
];

return [
    'class'=>'\crud\models\ar\NewsPage',
    'access'=>[
        ['allow', 'users'=>['@'], 'roles'=>['admin', 'sadmin', 'crud_news_pages_manager']],
        ['deny', 'users'=>['*']],
    ],
    'config'=>[
        'tablename'=>'news_pages',
        'definitions'=>[
            'column.pk',
            'column.create_time'=>['label'=>$t['attribute.create_time']],
            'column.update_time',
            'column.published',
            'column.title',
            'column.sef',
            'column.image',
            'column.text',
            'sort'=>['type'=>'INT(11), KEY(`sort`)', 'label'=>$t['attribute.sort']],
            'preview_text'=>['type'=>'TEXT', 'label'=>$t['attribute.preview_text']]
        ],
        'behaviors'=>[
            'seoBehavior'=>'\seo\behaviors\SeoBehavior',            
        ],
        'consts'=>[
            'ROLE_MANAGER'=>'crud_news_pages_manager',
            'TYPE_TOP'=>'1',
            'TYPE_CENTER'=>'3',
            'TYPE_BOTTOM'=>'5',
        ],
        'rules'=>[
            'safe',
            ['title', 'required'],
            ['sort, preview_text', 'safe'],
            ['sort', 'numerical', 'integerOnly'=>true],
            ['title, sef', 'length', 'max'=>255]
        ],
        'scopes'=>[
            'previewColumns'=>[
                'select'=>'php:new \CDbExpression(\'`t`.`id`, `t`.`create_time`, `t`.`image`, `t`.`published`, `t`.`sort`, `t`.`title`, `t`.`preview_text`, IF(LENGTH(`t`.`text`)>0, 1, 0) AS `has_text`\')'
            ]
        ],
        'methods'=>[
            'public $has_text;',
            'public static function getPages($limit=10) {
                return static::model()->previewColumns()->published()->findAll(["limit"=>$limit, "order"=>"`t`.`create_time` DESC, `t`.`sort` DESC, `t`.`id` DESC"]);
            }',
            'public function getTmbWidth(){
                return 350;
            }',
            'public function getTmbHeight(){
                return 220;
            }',
            'public function getPageUrl() {
                if($this->text || $this->has_text) {
                    return \crud\components\helpers\HCrudPublic::getViewUrl("'.$cid.'", $this->id);
                }
                return false;
            }',
            'public function beforeSave() {
                parent::beforeSave();
                if($this->owner->isNewRecord) {
                    if(!$this->owner->sort) {
                        $query="SELECT MAX(`sort`) + 5 FROM " . \common\components\helpers\HDb::qt($this->tableName()) . " WHERE 1=1";
                        $this->owner->sort=(int)\common\components\helpers\HDb::queryScalar($query);
                    }
                    $createTime=preg_replace(\'/[^1-9]/\', \'\', $this->create_time);
                    if(empty($createTime)) $this->create_time=new \CDbExpression("NOW()");
                }
                return true;
            }'            
        ]
    ],
    'public'=>[
        'access'=>[
            ['allow', 'users'=>['*'], 'actions'=>['index', 'view']],
        ],
        'routes'=>[
            'index'=>'news',
            'view'=>'news/<sef>'
        ],
        'index'=>[
            'title'=>'Новости',
            'view'=>'//crud/news_index',
            'listview'=>'//crud/news_listview'
        ],
        'view'=>[
            'view'=>'//crud/news_view'
        ]
    ],
    'menu'=>[
        'backend'=>['label'=>$t['menu.backend.label']]
    ],
    'buttons'=>[
        'create'=>['label'=>$t['buttons.create.label']],
    ],
    /* 'settings'=>[
        'title'=>$t['settings.title'],
        'attributes'=>[
            'page_size'=>$t['settings.attributes.page_size'],
            'meta_h1'=>$t['settings.attributes.meta_h1'],
            'meta_title'=>$t['settings.attributes.meta_title'],
            'meta_key'=>$t['settings.attributes.meta_key'],
            'meta_desc'=>$t['settings.attributes.meta_desc'],
            'text'=>$t['settings.attributes.text'],
        ],
        'tabs'=>[
            'main'=>[
                'title'=>$t['settings.tabs.main.title'],
                'attributes'=>[
                    'page_size'=>'number',
                    'text'=>'tinyMce',
                ]
            ],
            'seo'=>[
                'title'=>$t['settings.tabs.seo.title'],
                'attributes'=>[
                    'meta_h1',
                    'meta_title',
                    'meta_key',
                    'meta_desc'=>'textArea'
                ]
            ]
        ]
    ], */
    'crud'=>[
        'index'=>[
            'url'=>'/cp/crud/index',
            'title'=>$t['crud.index.title'],
            'gridView'=>[
                'id'=>'salePagesGridViewId',
                'dataProvider'=>[
                    'criteria'=>[
                        'select'=>'`t`.`id`, `t`.`create_time`, `t`.`image`, `t`.`published`, `t`.`sort`, `t`.`title`, `t`.`preview_text`, `t`.`sef`'
                    ],
                    'sort'=>['defaultOrder'=>'`t`.`sort` DESC, `t`.`create_time` DESC, `t`.`id` DESC'],
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
                        ]],
                        'headerHtmlOptions'=>['style'=>'width:15%'],                        
                    ],
                    [
                        'type'=>'column.title',
                        'header'=>$t['crud.index.gridView.columns.title.header'],
                        'headerHtmlOptions'=>['style'=>'width:70%;'],
                        'info'=>[
                            $t['crud.index.gridView.columns.title.info.preview_text']=>'$data->preview_text',
                            $t['crud.index.gridView.columns.title.info.link']=>'\CHtml::link("'.$t['crud.index.gridView.columns.title.info.link.title'].'", \crud\components\helpers\HCrudPublic::getViewUrl("'.$cid.'", $data->id), ["class"=>"btn btn-default btn-xs", "target"=>"_blank"])',
                        ]
                    ],
                    [
                        'name'=>'sort',
                        'header'=>'Сорт.',
                        'headerHtmlOptions'=>['style'=>'width:5%;text-align:center;white-space:nowrap;'],
                        'htmlOptions'=>['style'=>'text-align:center'],
                    ],
                    [
                        'name'=>'create_time',
                        'header'=>'Дата',
                        'headerHtmlOptions'=>['style'=>'width:10%;text-align:center'],
                        'htmlOptions'=>['style'=>'text-align:center'],
                        'value'=>'\common\components\helpers\HYii::formatDate($data->create_time, "dd.MM.yyyy")'
                    ],
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
            'title'=>$t['crud.create.title'],
        ],
        'update'=>[
            'url'=>['/cp/crud/update'],
            'title'=>$t['crud.update.title'],
        ],
        'delete'=>[
            'url'=>['/cp/crud/delete'],
        ],
        'form'=>[
            'htmlOptions'=>['enctype'=>'multipart/form-data'],
        ],
        'tabs'=>[
            'main'=>[
                'title'=>$t['tabs.main.title'],
                'attributes'=>function(&$model) {
                    if($model->isNewRecord) $model->create_time=date('Y-m-d H:i:s');
                    $attributes=[
                        'published'=>'checkbox',
                        'sort'=>[
                            'type'=>'number',
                            'params'=>['htmlOptions'=>['class'=>'form-control w10']]
                        ],
                        'title',
                        'sef'=>'alias',
                        'create_time'=>'date',
                        'preview_text'=>'textArea', /* [
                            'type'=>'tinyMce',
                            'params'=>['full'=>false]
                        ], /**/
                        'image'=>[
                            'type'=>'common.ext.file.image',
                            'params'=>[
                                'tmbWidth'=>$model->getTmbWidth(),
                                'tmbHeight'=>$model->getTmbHeight()
                            ]
                        ],
                        'text'=>'tinyMce',
                    ];
                    return $attributes;
                }
            ],      
            'seo'=>[
                'title'=>$t['tabs.seo.title'],
                'use'=>['seo.config.crud.seo', 'crud.form']
            ]
        ]
    ]
];
