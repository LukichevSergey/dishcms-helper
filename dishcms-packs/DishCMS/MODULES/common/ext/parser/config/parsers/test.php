<?php
/**
 * Конфигурации парсера товаров с сайта https://www.webscraper.io/test-sites/
 *
 */
return [
    // http://www.local/cmsparser/parse.php
    // https://github.com/FriendsOfPHP/Goutte?source=post_page---------------------------
    // https://simplehtmldom.sourceforge.io/
    // https://www.webscraper.io/test-sites/e-commerce/static/computers/tablets
    
    'domain'=>'http://webscraperio.us-east-1.elasticbeanstalk.com',
    'entry'=>'/test-sites/e-commerce/static/computers/tablets',
    'limit'=>10,
    
    /** для тестирования работы итератора (свои обработчики создания и итерации) * /
    'iterator'=>[
        'create'=>function($iteratorProcess) {
            \Yii::app()->user->setState('example_iteration_percent', 0); 
            return ['step'=>10];
        },        
        'next'=>function($iteratorProcess) {
            $percent=(float)\Yii::app()->user->getState('example_iteration_percent', 0) + $iteratorProcess->getDataParam('step', 10);
            \Yii::app()->user->setState('example_iteration_percent', $percent);
            return $percent;
        }
    ],
    /**/
    
    'groups'=>[
        'categories'=>[
            'recursive'=>true,
            'precontent'=>['dom', 'div.container.test-site'],
            'content'=>[
                'model'=>'\Category',
                'precontent'=>['dom', 'div.container.test-site div.row div.col-md-3 ul#side-menu'],
                'pattern'=>['dom', 'ul#side-menu li a[class*=category]'],
                'syncs'=>['title'],
                'required'=>['title'],
                'attributes'=>[
                    'sync_code'=>function($page, $blockContent) {
                        return md5($page->normalizeUrl($page->processDomAttributePattern($blockContent, ['dom', 'a', 'href'])));
                    },
                    'title'=>['dom', 'a', true],
                ],
                'onDublicateSQL'=>'`sync_code`=VALUES(`sync_code`)',
            ],
            'links'=>[
                'precontent'=>['dom', 'div.container.test-site div.row div.col-md-3 ul#side-menu'],
                'pattern'=>['dom', 'ul#side-menu li a'],
            ],
            'groups'=>[
                'products'=>[
                    'content'=>[
                        'model'=>'\Product',
                        'precontent'=>['dom', 'div.container.test-site div.row div.col-md-9 div.row'],
                        'pattern'=>['dom', 'div.row div.col-sm-4.col-lg-4.col-md-4'],
                        'syncs'=>['sync_code'],
                        'required'=>['sync_code'],
                        'attributes'=>[
                            'sync_code'=>function($page, $blockContent) {
                                return preg_replace('/^.*?([0-9]+)$/', '$1', $page->processDomAttributePattern($blockContent, ['dom', 'div div.caption h4 a.title', 'href']));
                            },
                            'category_id'=>function($page, $blockContent) {
                                if($category=\Category::model()->findByAttributes(['sync_code'=>md5(preg_replace('/\?.*?$/', '', $page->getUrl()))])) {
                                    return $category->id;
                                }                                
                                return 0;
                            },
                            'title'=>['dom', 'div div.caption h4 a.title', true],
                            //'photo'=>['image', ['dom', 'div div.thumbnail img.img-responsive']],
                            'price'=>function($page, $blockContent) {
                                return preg_replace('/[^0-9.]+/', '', $page->processDomAttributePattern($blockContent, ['dom', 'div div.caption h4.price', true]));
                            }
                        ],
                        'onDublicateSQL'=>'`price`=VALUES(`price`),`created`=NOW()'
                    ],
                    'links'=>[
                        'precontent'=>['dom', 'div.container.test-site div.row div.col-md-9 div.row'],
                        'pattern'=>['dom', 'div.row div.col-sm-4.col-lg-4.col-md-4 div.caption h4 a.title'],
                    ],
                    'pagination'=>[
                        'precontent'=>['dom', 'div.container.test-site div.row div.col-md-9 ul.pagination'],
                        'pattern'=>['dom', 'ul.pagination li a']
                    ],
                    'groups'=>[
                        'product'=>[
                            'content'=>[
                                'precontent'=>['dom', 'div.container.test-site div.row div.col-md-9 div.row div.col-lg-10'],
                                'pattern'=>['dom', 'div.col-lg-10'],
                                'model'=>'\Product',
                                'syncs'=>['sync_code'],
                                'required'=>['sync_code', 'title'],
                                'attributes'=>[
                                    'sync_code'=>function($page, $blockContent) {
                                        return preg_replace('/^.*?([0-9]+)$/', '$1', $page->getUrl());
                                    },
                                    'title'=>['dom', 'div div.caption h4[!class]', true],
                                    'description'=>['dom', 'div div.caption p.description'],
                                ],
                                'onDublicateSQL'=>'`title`=VALUES(`title`),`description`=VALUES(`description`),`created`=NOW()'
                            ]
                        ]
                    ]
                ],
            ]
        ],
    ]
];