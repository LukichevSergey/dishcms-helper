<?php

use common\components\helpers\HHtml;

return [
    'class'=>'\crud\models\ar\DeliveryArea',
    'config'=>[
        'tablename'=>'delivery_areas',
        'definitions'=>[
            'column.pk',
            'column.title',
            'column.create_time',
            'column.update_time',
            'column.published',
            'column.sort',
            'price'=>['type'=>'DECIMAL(15,2)', 'label'=>'Стоимость доставки'],
        ],
        'rules'=>[
            'safe',
            ['title', 'required'],
            ['price', 'numerical', 'min'=>0],
        ],        
    ],
    'menu'=>[
        'backend'=>['label'=>'Районы доставки']
    ],
    'buttons'=>[
        'create'=>['label'=>'Добавить район'],
    ],
    'crud'=>[
        'index'=>[
            'url'=>'/cp/crud/index',
            'title'=>'Районы доставки',
            'gridView'=>[
                'dataProvider'=>[
                    'sort'=>['defaultOrder'=>'`sort`, `title`']
                ],
                'emptyText'=>'Районов не найдено',
                'columns'=>[
                    'column.id',
                    'column.title',
                    [
                        'name'=>'price',
                        'type'=>'raw',
                        'headerHtmlOptions'=>['style'=>'width:20%;text-align:center'],
                        'htmlOptions'=>['style'=>'text-align:center'],
                        'value'=>function($data) {
                            if((float)$data->price === 0.00) {
                                return \CHtml::tag('span', ['class'=>'label label-default'], 'бесплатно');
                            }
                            return HHtml::price($data->price) . ' руб.';
                        }
                    ],
                    'common.ext.sort',
                    [
                        'name'=>'published',
                        'type'=>'common.ext.published',
                        'header'=>'Опубл.',
                        'headerHtmlOptions'=>['style'=>'width:10%;text-align:center'],
                        'htmlOptions'=>['style'=>'text-align:center']
                    ],                    
                    'crud.buttons'
                ]
            ]
        ],
        'create'=>[
            'url'=>'/cp/crud/create',
            'title'=>'Новый район доставки',
        ],
        'update'=>[
            'url'=>['/cp/crud/update'],
            'title'=>'Редактирование района доставки',
        ],
        'delete'=>[
            'url'=>['/cp/crud/delete'],
        ],
        'form'=>[
            'attributes'=>function($model) {
                $attributes=[
                    'published'=>'checkbox',
                    'sort'=>[
                        'type'=>'number',
                        'params'=>[
                            'htmlOptions'=>['class'=>'form-control w10', 'step'=>1],
                        ]
                    ],
                    'title',
                    'price'=>[
                        'type'=>'number',
                        'params'=>[
                            'htmlOptions'=>['class'=>'form-control w10 inline', 'step'=>1, 'min'=>0, 'style'=>'margin-right:5px'],
                            'unit'=>'руб.'
                        ]
                    ]
                ];

                return $attributes;
            }
        ]
    ]
];

    