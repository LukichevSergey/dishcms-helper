<?php
use common\components\helpers\HYii as Y;

return [
    'items'=>[
        // страницы сайта
        'pages'=>[
            'title'=>'Страницы сайта',
            'loc'=>'/',
            'class'=>'\Page',
            'criteria'=>['select'=>'id, update_time, alias'],
            'loc'=>function($model) { return ($model->alias == 'index') ? '/' : Y::createUrl('/site/page', ['id'=>$model->id]); },
            'lastmod'=>'update_time',            
        ],

        // новости
        'news'=>[
            'title'=>function() { return \D::cms('events_title', 'Новости'); },
            'loc'=>function() { return Y::createUrl('/site/events'); },
            'priority'=>1,
            'items'=>[
                'events'=>[
                    'title'=>function() { return \D::cms('events_title', 'Новости'); },
                    'class'=>'\Event',
                    'criteria'=>['select'=>'id, update_time'],
                    'loc'=>function($model) { return Y::createUrl('/site/event', ['id'=>$model->id]); },
                    'lastmod'=>'update_time',
                ]
            ]
        ],

        // акции
        'sale'=>[
            'title'=>function() { return \D::cms('sale_title', 'Акции'); },
            'disabled'=>function() { return !\D::yd()->isActive('sale'); },
            'loc'=>function() { return Y::createUrl('/sale'); },
            'priority'=>1,
            'items'=>[
                'sales'=>[
                    'title'=>function() { return \D::cms('sale_title', 'Акции'); },
                    'class'=>'\Sale',
                    'criteria'=>['select'=>'id, create_time'],
                    'loc'=>function($model) { return Y::createUrl('/sale/view', ['id'=>$model->id]); },
                    'lastmod'=>'create_time',
                ]
            ]
        ],

        // фотогалерея
        'gallery'=>[
            'title'=>function() { return \D::cms('gallery_title', 'Фотогалерея'); },
            'disabled'=>function() { return !\D::yd()->isActive('gallery'); },
            'loc'=>function() { return Y::createUrl('/gallery'); },
            'priority'=>1,
            'items'=>[
                'alboms'=>[
                    'title'=>function() { return \D::cms('gallery_title', 'Фотогалерея'); },
                    'class'=>'\Gallery',
                    'criteria'=>['scopes'=>'published', 'select'=>'id, update_time'],
                    'loc'=>function($model) { return Y::createUrl('/gallery/album', ['id'=>$model->id]); },
                    'lastmod'=>'update_time',
                ]
            ]
        ],

        // отзывы
        'reviews'=>[
            'title'=>function() { return 'Отзывы'; },
            'disabled'=>function() { return !\D::yd()->isActive('reviews'); },
            'loc'=>function() { return Y::createUrl('/reviews'); },
            'priority'=>1,
            'items'=>[
                'review'=>[
                    'title'=>function() { return 'Отзывы'; },
                    'class'=>'\reviews\models\Review',
                    'criteria'=>['scopes'=>'actived', 'select'=>'id, create_time'],
                    'loc'=>function($model) { return Y::createUrl('/reviews/default/view', ['id'=>$model->id]); },
                    'lastmod'=>'create_time',
                ]
            ]
        ],

        // вопрос-ответ
        'questions'=>[
            'title'=>'Вопрос-Ответ',
            'disabled'=>function() { return !\D::yd()->isActive('question'); },
            'loc'=>function() { return Y::createUrl('/question'); },
            'priority'=>1,
            'lastmod'=>function() {
                if($question=\Question::model()->find(['order'=>'created DESC'])) {
                    return $question->created;
                }
            },
        ],

        // бренды
        'brands'=>[
            'title'=>function() { return 'Бренды'; },
            'disabled'=>function() { return !(\D::yd()->isActive('shop') && \D::cms('shop_enable_brand')); },
            'loc'=>function() { return Y::createUrl('/brands'); },
            'priority'=>1,
            'items'=>[
                'brand'=>[
                    'title'=>function() { return \D::cms('sale_title', 'Акции'); },
                    'class'=>'\Brand',
                    'criteria'=>['select'=>'id, alias, update_time'],
                    'loc'=>function($model) { return Y::createUrl('/brand/view', ['alias'=>$model->alias]); },
                    'lastmod'=>'update_time',
                ]
            ]
        ],

        // каталог товаров
        'catalog'=>[
            'title'=>function() { return \D::cms('shop_title', 'Каталог'); },
            'disabled'=>function() { return !\D::yd()->isActive('shop'); },
            'loc'=>function() { return Y::createUrl('/catalog'); },
            'priority'=>1,
            'items'=>[
                'category'=>[
                    'title'=>function() { return \D::cms('shop_title', 'Каталог'); },
                    'class'=>'\Category',
                    'criteria'=>['select'=>'id, lft, rgt, level, root, update_time', 'order'=>'root,lft'],
                    'loc'=>function($model) { return Y::createUrl('/shop/category', ['id'=>$model->id]); },
                    'lastmod'=>'update_time',
                    'priority'=>function($model) {
                        if($model->meta) {
                            if($priority=$model->meta->priority) return $priority;
                            else return ($model->level > 1) ? sprintf('%0.1f',1.5/$model->level) : 1;
                        }
                        return 1;
                    },
                    'items'=>[
                        'product'=>[
                            'title'=>function() { return \D::cms('shop_title', 'Каталог'); },
                            'class'=>'\Product',
                            'criteria'=>['scopes'=>'visibled', 'select'=>'id, update_time'],
                            'loc'=>function($model) { return Y::createUrl('/shop/product', ['id'=>$model->id]); },
                            'lastmod'=>'update_time'                            
                        ]
                    ]
                ]
            ]
        ]
    ]
];