<?php
use common\components\helpers\HYii as Y;

return [
    'class'=>'\crud\models\ar\SeoMetaTag',
    'config'=>[
        'tablename'=>'seo_meta_tags',
        'definitions'=>[
            'column.pk',
            'column.published',
            'column.create_time',
            'column.update_time',
            'column.title'=>['label'=>'Заголовок браузера'],
            'meta_h1'=>['type'=>'string', 'label'=>'H1'],
            'meta_desc'=>['type'=>'TEXT', 'label'=>'META: Description'],
            'meta_keywords'=>['type'=>'TEXT', 'label'=>'META: Keywords'],
            'url'=>['type'=>'TEXT', 'label'=>'URL'],
            'text'=>['type'=>'LONGTEXT', 'label'=>'Текст']
        ],
        'rules'=>[
        	'safe',
        	['url, meta_h1, meta_desc, meta_keywords, text', 'safe']
        ],
        'methods'=>[
        	'private static $cache=[];',
        	'public function beforeSave() { parent::beforeSave(); if(preg_match(\'#^(http|https)://[^/]+/(.*)$#i\', $this->url, $m)) { $this->url="/".$m[2]; } return true; }',
        	'public static function getByUrl($url=null) {
	        	if(!$url) { if(!empty($GLOBALS["__ORIGIN_REQUEST_URI"])){$url=$GLOBALS["__ORIGIN_REQUEST_URI"];}else{$url=$_SERVER["REQUEST_URI"];} }
	        	$hash=md5($url);if(!array_key_exists($hash, static::$cache)) {
        			static::$cache[$hash]=\common\components\helpers\HDb::queryRow("SELECT * FROM `seo_meta_tags` WHERE `published`=1 AND `url`=:url", ["url"=>$url]);
        		}
        		return static::$cache[$hash];
        	}',
        	'public static function getMetaTags($url=null) {
        		if($meta=static::getByUrl($url)) {
        			return [
        				"meta_h1"=>$meta["meta_h1"],
						"meta_title"=>$meta["title"],        				
        				"meta_desc"=>$meta["meta_desc"],
        				"meta_key"=>$meta["meta_keywords"]
        			];
        		}
        		return null;
        	}',
        	'public static function getTextByUrl($url=null) {
	        	if($meta=static::getByUrl($url)) {
	        		return $meta["text"];
	        	}
	        	return null;
        	}'
        ]
    ],
    'menu'=>[
        'backend'=>['label'=>'SEO Страницы']
    ],
    'buttons'=>[
        'create'=>['label'=>'Добавить страницу'],
    ],
    'crud'=>[
        'index'=>[
            'url'=>'/cp/crud/index',
            'title'=>'Страницы',
            'gridView'=>[
                'dataProvider'=>[
                    'sort'=>['defaultOrder'=>'`create_time` DESC']
                ],
                'summaryText'=>'Страницы {start} - {end} из {count}',
                'columns'=>[
                    'column.id',
                    [
                    	'type'=>'column.title',
                    	'info'=>[
                    		'URL'=>'$data->url ?: "Установленный по умолчанию"',
                    		'Заголовок браузера'=>'$data->title ?: "Установленный по умолчанию"',
                    		'H1'=>'$data->meta_h1 ?: "Установленный по умолчанию"',
                    		'META: Description'=>'$data->meta_desc ?: "Установленный по умолчанию"',
                    		'META: Keywords'=>'$data->meta_keywords ?: "Установленный по умолчанию"'
                    	],
                    	'htmlOptions'=>['style'=>'max-width:600px;']
                    ],
                    [
                        'name'=>'create_time',
                        'header'=>'Дата',
                        'type'=>'raw',
                        'headerHtmlOptions'=>['style'=>'width:10%;text-align:center;white-space:nowrap;'],
                        'htmlOptions'=>['style'=>'width:10%;text-align:center;'],
                        'value'=>function($data) {
                            $value=Y::formatDate($data->create_time);
                            if(!!preg_replace('/[^1-9]+/', $data->update_time)) {
                            	$value.='<br/><br/><b>обновлен</b>' . Y::formatDate($data->update_time);
                            }
                            return $value;
                        }
                    ],
                    [
                        'name'=>'published',
                        'header'=>'Акт.',
                        'type'=>'common.ext.published',
                        'headerHtmlOptions'=>['style'=>'width:10%;text-align:center;white-space:nowrap;']
                    ],
                    'crud.buttons'
                ]
            ]
        ],
        'create'=>[
            'url'=>'/cp/crud/create',
            'title'=>'Новая страница',
        ],
        'update'=>[
            'url'=>'/cp/crud/update',
            'title'=>'Редактировать страницу',
        ],
        'delete'=>[
            'url'=>'/cp/crud/delete',
        ],
        'tabs'=>[
        	'seo'=>[
        		'title'=>'SEO',
		        'attributes'=>[
		            'published'=>'checkbox',
		            'url'=>'textArea',
		            'title'=>[
		            	'type'=>'text',
		            	'params'=>['htmlOptions'=>['class'=>'form-control w100']],
		            ],
		            'meta_h1'=>[
		            	'type'=>'text',
		            	'params'=>['htmlOptions'=>['class'=>'form-control w100']],
		            ],
		            'meta_desc'=>'textArea',
		            'meta_keywords'=>'textArea',
		        ],
		    ],
		    'text'=>[
		    	'title'=>'Текст',
		    	'attributes'=>[
		            'text'=>'tinyMce',
	            ]
		    ]
        ]
    ],
];
