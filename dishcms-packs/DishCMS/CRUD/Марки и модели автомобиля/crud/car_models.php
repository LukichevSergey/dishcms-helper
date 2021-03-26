<?php
/**
 * Файл настроек модели \PriceSubSection
 */
use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HRequest as R;
use crud\models\ar\CarBrand;

$brand=(($brandId=(int)R::get('brand')) && class_exists('\crud\models\ar\CarBrand')) ? CarBrand::modelById((int)R::get('brand')) : null;
$onBeforeLoad=function() use ($brand) { if(empty($brand)) { R::e404(); }};

return [
	'class'=>'\crud\models\ar\CarModel',
    'config'=>[
        'tablename'=>'car_models',
        'definitions'=>[
            'column.pk',
            'foreign.brand_id'=>['label'=>'Марка'],
            'column.title',
            'column.published',
        ],
        'rules'=>[
            'safe',
            ['brand_id, title', 'required'],
        ],
    ],
	'menu'=>[
		'backend'=>['label'=>'Модели автотранспорта', 'disabled'=>true]
	],
	'buttons'=>[
		'create'=>['label'=>'Добавить модель'],
	],
	'crud'=>[
		'onBeforeLoad'=>$onBeforeLoad,
		'breadcrumbs'=>[
			'Марки автотраспорта'=>\Yii::app()->createUrl('/cp/crud/index', ['cid'=>"car_brands"]),
		    ($brand ? $brand->title : '')
		],
		'index'=>[
		    'url'=>['/cp/crud/index', 'brand'=>($brand ? $brand->id : '')],
		    'title'=>'Модели марки &laquo;' . ($brand ? $brand->title : '') . '&raquo;',
			'titleBreadcrumb'=>'Модели',
			'gridView'=>[ 
				'dataProvider'=>[
					'criteria'=>[
						'select'=>'`t`.`id`, `t`.`brand_id`, `t`.`title`, `t`.`published`',
						'condition'=>'brand_id=:brandId',
						'params'=>[':brandId'=>($brand ? $brand->id : 'NULL')]
					],
				    'sort'=>['defaultOrder'=>'`t`.`title` ASC']
				],
				'columns'=>[
					[
						'name'=>'id',
						'header'=>'#',
						'headerHtmlOptions'=>['style'=>'width:5%'],
					],
					'column.title',
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
		    'url'=>['/cp/crud/create', 'brand'=>($brand ? $brand->id : '')],
			'title'=>'Новая модель',
		],
		'update'=>[
		    'url'=>['/cp/crud/update', 'brand'=>($brand ? $brand->id : '')],
			'title'=>'Редактирование модели',
		],
		'delete'=>[
		    'url'=>['/cp/crud/delete', 'brand'=>($brand ? $brand->id : '')]
		],
		'form'=>[
		    'attributes'=>function(&$model) use ($brand) {
                if(!$model->brand_id) {
		          $model->brand_id=$brand->id;
                }
                
		        $attributes=[
		            'published'=>'checkbox'
		        ];
		        
		        $attributes['brand_id']=[
		            'type'=>'dropDownList',
		            'params'=>['data'=>CarBrand::model()->listData('title')],
		        ];
		        
		        $attributes[]='title';
		        
		        return $attributes;
		    }
		]
	]
];
