<?php
return [
    'class'=>'\crud\models\ar\City',
    'config'=>[
        'tablename'=>'cities',
        'definitions'=>[
            'column.pk',
            'column.title'=>['label'=>'Город'],
            'column.published',
            'url'=>['type'=>'string', 'label'=>'URL сайта'],
        ],
        'rules'=>[
            'safe',
            ['title, url', 'required'],
            ['url', 'safe'],
        ],
        'behaviors'=>[
            '\common\ext\sort\behaviors\SortBehavior'
        ],
        'methods'=>[
            'private static $currentCity=null;',
            'public static function getCities($excludeCurrent=false) {
                $cities=static::model()->cache(1)->published()->scopeSort("cities")->listData("url", null, null, "title");
                if($excludeCurrent) {
                    if($current=static::getCurrentCity()) {
                        foreach($cities as $title=>$url) {
                            if($title === $current->title) {
                                unset($cities[$title]);
                                break;
                            }
                        }
                    }
                }
                return $cities;
            }',
            'public static function getCurrentCity() {
                if(static::$currentCity === null) {
                    static::$currentCity=false;
                    if($cities=static::model()->cache(1)->published()->scopeSort("cities")->findAll()) {
                        $serverName=mb_strtolower($_SERVER["SERVER_NAME"]);
                        foreach($cities as $city) {
                            if($serverName === $city->getDomain()) {
                                static::$currentCity=$city;
                                break;
                            }
                        }
                    }
                }
                return static::$currentCity;
            }',
            'public function getDomain() {
                $domain=$this->url;
                if(preg_match(\'#^(http|https)://([^/]+)#i\', $this->url, $m)) $domain=$m[2];
                elseif(preg_match(\'#^([^/]+)#i\', $this->url, $m)) $domain=$m[1];
                return mb_strtolower($domain);
            }'
        ]
    ],
    'menu'=>[
        'backend'=>['label'=>'Города']
    ],
    'buttons'=>[
        'create'=>['label'=>'Добавить город'],
    ],
    'crud'=>[
        'index'=>[
            'url'=>'/cp/crud/index',
            'title'=>'Города',
            'gridView'=>[
                'dataProvider'=>[
                    'criteria'=>[
                        'select'=>'`t`.`id`, `t`.`title`, `t`.`url`, `t`.`published`',
                    ],
                ],
                'sortable'=>[
                    'url'=>'/cp/crud/sortableSave',
                    'category'=>'cities'
                ],
                'columns'=>[
                    'column.id',
                    'column.title',
                    'url',
                    [
                        'name'=>'published',
                        'header'=>'Опубликовать',
                        'headerHtmlOptions'=>['style'=>'text-align:center;width:15%'],
                        'type'=>'common.ext.published'
                    ],
                    'crud.buttons'
                ]
            ]
        ],
        'create'=>[
            'url'=>'/cp/crud/create',
            'title'=>'Новый город',
        ],
        'update'=>[
            'url'=>['/cp/crud/update'],
            'title'=>'Редактирование города',
        ],
        'delete'=>[
            'url'=>['/cp/crud/delete'],
        ],
        'form'=>[
            'attributes'=>[
                'published'=>'checkbox',
                'title',
                'url',
            ]
        ]
    ]
];

    