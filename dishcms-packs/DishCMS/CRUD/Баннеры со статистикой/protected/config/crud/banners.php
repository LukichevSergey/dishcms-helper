<?php
/**
 * \D::cms("banners_lifetime", 1800);
 * $banners=\crud\models\ar\Banner::model()->byPriority(3)->published()->findAll();
 * foreach($banners as $banner) { $banner->incShows(); }
 */
return [
    'class'=>'\crud\models\ar\Banner',
    'config'=>[
        'tablename'=>'banners',
        'definitions'=>[
            'column.pk',
            'column.image',
            'column.create_time',
            'column.update_time',
            'column.published',
            'url'=>['type'=>'string', 'label'=>'Ссылка'],
            'priority'=>['type'=>'TINYINT DEFAULT 1', 'label'=>'Приоритет'],
            'shows'=>['type'=>'BIGINT UNSIGNED NOT NULL DEFAULT 0', 'label'=>'Показано'],
            'shows_total'=>['type'=>'BIGINT UNSIGNED NOT NULL DEFAULT 0', 'label'=>'Всего показов']
        ],
        'rules'=>[
            'safe',
            ['url', 'safe'],
            ['priority', 'required'],
            ['priority', 'numerical', 'integerOnly'=>true, 'min'=>1, 'max'=>10]
        ],
        'methods'=>[
            'const COOKIE_CRYPT_KEY=\'bBR).PLZa3cV4(>m\';',
            'private $lastPublished;',
            'public function byPriority($limit=6) {
                $cookieId=md5("banners_by_priority_ids");
                if(!empty($_COOKIE[$cookieId])) {
                    $decrypted=\common\components\helpers\HHash::srDecrypt($_COOKIE[$cookieId], self::COOKIE_CRYPT_KEY);
                    if(preg_match("/^[0-9$]+$/", $decrypted)) {
                        $ids=$_COOKIE[$cookieId];
                    } 
                }

                if(empty($ids)) {
                    $ids=\common\components\helpers\HDb::queryColumn("SELECT `id` FROM `banners` WHERE `published`=1 ORDER BY (`shows`/`priority`*10) LIMIT " . (int)$limit);
                    if(!empty($ids)) {
                        shuffle($ids);
                        $ids=implode(",", $ids);
                        setcookie($cookieId, \common\components\helpers\HHash::srEcrypt($ids, self::COOKIE_CRYPT_KEY), time() + \D::cms("banners_lifetime", 1800), "/");
                    }
                }

                $this->getDbCriteria()->mergeWith([
                    "condition"=>(empty($ids) ? "1<>1" : "`t`.`id` IN ({$ids})"),
                    "order"=>"RAND()"
                ]);

                return $this;
            }',
            'public function incShows() {
                \common\components\helpers\HDb::query("UPDATE `banners` SET `shows`=`shows`+1, `shows_total`=`shows_total`+1 WHERE `id`=".(int)$this->id);
            }',
            'public function resetShows() {
                \common\components\helpers\HDb::query("UPDATE `banners` SET `shows`=0");
            }',
            'public function beforeSave() {
                parent::beforeSave();
                $createTime=preg_replace("/[^1-9]/", "", $this->create_time);
                if(empty($createTime)) $this->create_time=new \CDbExpression("NOW()");

                $this->lastPublished=null;
                if(!$this->isNewRecord && $this->id) {
                    $this->lastPublished=(int)\common\components\helpers\HDb::queryScalar("SELECT `published` FROM `banners` WHERE `id`=".(int)$this->id);
                }

                return true;
            }',
            'public function afterSave() {
                parent::afterSave();
                if($this->isNewRecord || ($this->id && ($this->lastPublished !== null) && is_numeric($this->published) && ($this->published != $this->lastPublished))) {
                    $this->resetShows();
                }
            }'
        ]
    ],
    'menu'=>[
        'backend'=>['label'=>'Баннеры']
    ],
    'buttons'=>[
        'create'=>['label'=>'Добавить баннер'],
    ],
    'crud'=>[
        'index'=>[
            'url'=>'/cp/crud/index',
            'title'=>'Баннеры',
            'gridView'=>[
                'dataProvider'=>[
                    'criteria'=>[
                        'select'=>'id, url, image, priority, shows, shows_total, published, create_time'
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
                        'name'=>'url'
                    ],
                    [
                        'name'=>'priority',
                        'headerHtmlOptions'=>['style'=>'width:12%;text-align:center'],
                        'htmlOptions'=>['style'=>'text-align:center']
                    ],                    
                    [
                        'name'=>'shows',
                        'headerHtmlOptions'=>['style'=>'width:12%;text-align:center'],
                        'htmlOptions'=>['style'=>'text-align:center']
                    ],                    
                    [
                        'name'=>'shows_total',
                        'headerHtmlOptions'=>['style'=>'width:14%;text-align:center;font-size:0.9em;'],
                        'htmlOptions'=>['style'=>'text-align:center']
                    ],                    
                    [
                        'name'=>'create_time',
                        'header'=>'Добавлен',
                        'headerHtmlOptions'=>['style'=>'width:12%;text-align:center'],
                        'htmlOptions'=>['style'=>'text-align:center']
                    ],
                    [
                        'name'=>'published',
                        'header'=>'Активен',
                        'type'=>'raw',
                        'value'=>'$data->published ? "<span class=\"label label-success\">Да</span>" : "<span class=\"label label-danger\">Нет</span>"',
                        'headerHtmlOptions'=>['style'=>'width:10%;text-align:center'],
                        'htmlOptions'=>['style'=>'text-align:center']
                    ],
                    'crud.buttons'
                ]
            ]
        ],
        'create'=>[
            'url'=>'/cp/crud/create',
            'title'=>'Новый баннер',
        ],
        'update'=>[
            'url'=>['/cp/crud/update'],
            'title'=>'Редактирование баннера',
        ],
        'delete'=>[
            'url'=>['/cp/crud/delete'],
        ],
        'form'=>[
            'htmlOptions'=>['enctype'=>'multipart/form-data'],
            'attributes'=>[
                'published'=>'checkbox',
                'priority'=>[
                    'type'=>'number',
                    'params'=>[
                        'htmlOptions'=>['class'=>'form-control w10']
                    ]
                ],
                'url',
                'image'=>'common.ext.file.image',
            ]
        ]
    ]
];

    