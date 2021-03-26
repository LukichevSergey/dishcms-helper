<?php
return [
    'class'=>'\crud\models\ar\HeaderBanner',
    'config'=>[
        'tablename'=>'header_banners',
        'definitions'=>[
            'column.pk',
            'column.image',
            'column.create_time',
            'column.update_time',
            'column.published',
            'url'=>['type'=>'string', 'label'=>'Ссылка'],
        ],
        'rules'=>[
            'safe',
            ['url', 'safe'],
        ],
        'methods'=>[
            'const COOKIE_CRYPT_KEY=\'w6M>Q"Ya~~/,C#Y$RMFmC+Ef\';',
            'public static function getBanner() {
                $banner=static::model()->byRand()->published()->find();
                if($banner && !$banner->published) {
                    $banner=static::model()->byRand(true)->published()->find();
                }
                return $banner;
            }',
            'public function byRand($reload=false){
                $idx=0;
                $max=0;
                $cookieId=md5("header_banners_by_rand_ids");
                $cookieIdIdx=md5("header_banners_by_rand_idx");
                if(!$reload && !empty($_COOKIE[$cookieId])) {
                    $decryptedIds=\common\components\helpers\HHash::srDecrypt($_COOKIE[$cookieId], self::COOKIE_CRYPT_KEY);
                    $decryptedIdx=0;
                    if(!empty($_COOKIE[$cookieIdIdx])) {
                        $decryptedIdx=(int)\common\components\helpers\HHash::srDecrypt($_COOKIE[$cookieIdIdx], self::COOKIE_CRYPT_KEY);
                    }
                    if(preg_match("/^[0-9,]+$/", $decryptedIds)) {
                        $ids=$decryptedIds;
                        $idx=$decryptedIdx;
                    } 
                }

                if($reload || empty($ids)) {
                    $idx=0;
                    $ids=\common\components\helpers\HDb::queryColumn("SELECT `id` FROM `header_banners` WHERE `published`=1");
                    if(!empty($ids)) {
                        shuffle($ids);
                        $ids=implode(",", $ids);
                        setcookie($cookieId, \common\components\helpers\HHash::srEcrypt($ids, self::COOKIE_CRYPT_KEY), 0, "/");
                    }
                }

                $max=substr_count($ids, ",");
                if($idx > $max) $idx=0;
                setcookie($cookieIdIdx, \common\components\helpers\HHash::srEcrypt($idx + 1, self::COOKIE_CRYPT_KEY), 0, "/");

                $this->getDbCriteria()->mergeWith([
                    "condition"=>(empty($ids) ? "1<>1" : "`t`.`id` IN ({$ids}) AND `t`.`id`=ELT(" . ($idx + 1) . ",{$ids})"),
                    "limit"=>1
                ]);

                return $this;
            }',
            'public function beforeSave() {
                parent::beforeSave();
                $createTime=preg_replace("/[^1-9]/", "", $this->create_time);
                if(empty($createTime)) $this->create_time=new \CDbExpression("NOW()");
                return true;
            }',
        ]
    ],
    'menu'=>[
        'backend'=>['label'=>'Баннеры в шапке']
    ],
    'buttons'=>[
        'create'=>['label'=>'Добавить баннер'],
    ],
    'crud'=>[
        'index'=>[
            'url'=>'/cp/crud/index',
            'title'=>'Баннеры в шапке',
            'gridView'=>[
                'dataProvider'=>[
                    'criteria'=>[
                        'select'=>'id, url, image, published, create_time'
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
                        'name'=>'create_time',
                        'header'=>'Добавлен',
                        'headerHtmlOptions'=>['style'=>'width:15%;text-align:center'],
                        'htmlOptions'=>['style'=>'text-align:center']
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
                'url',
                'image'=>'common.ext.file.image',
            ]
        ]
    ]
];

    