Модуль "Комментарии" (только для моделей \common\components\base\ActiveRecord)

1) Добавить модуль в основные настройки модуля extend (/protected/modules/extend/config/main.php

	'modules'=>[
		...
		'comments'=>['class'=>'\extend\modules\comments\CommentsModule', 'autoload'=>true]
	],
	
2) Добавить доступные модели в файл конфигурации параметров (/protected/config/params.php)

	'extend'=>['modules'=>[
	    'comments'=>[
	        'models'=>[
	            'Товары'=>'\Product'
	        ]
	    ]
    ]],
    
	где, сокращенная запись:
	'models'=>[
        'Товары'=>'\Product',
        ...
    ]
    
    а, полная запись:
    'models'=>[ 
	    'Товары'=>[
	        '\Product', 
	        'itemLabel'=>'Товар',
	        'attributeTitle'=>'title',
            'parent'=>['\Category', 'category_id', 'attributeId'=>'id', 'attributeTitle'=>'title', 'label'=>'Категории', 'itemLabel'=>'Категория']
	    ]
	    ...
	]


3) Отображение списка комментариев
				<?php $this->widget('\extend\modules\comments\widgets\CommentsList', [
                    'model'=>$product,
                    'model_id'=>$product->id,
                    'pageSize'=>\D::shop('reviews_limit'),
                    'view'=>'application.views.shop._reviews',
                    'itemView'=>'application.views.shop._reviews_item',
					// ПРИ ИСПОЛЬЗОВАНИИ НА СТРАНИЦЕ CRUD PUBLIC VIEW
					'dataProviderOptions'=>['pagination'=>['params'=>['cid'=>'<<ВСТАВЬТЕ CRUD ID МОДЕЛИ К КОТОРОЙ ОТОБРАЖАЮТСЯ КОММЕНТАРИИ>>', 'id'=>$model->id<<ЗДЕСЬ $model ОБЪЕКТ CRUD МОДЕЛИ, К КОТОРОЙ ОТОБРАЖАЮТСЯ КОММЕНТАРИИ]]]
                ]); ?>


4) 
