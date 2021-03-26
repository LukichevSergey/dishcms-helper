<?php

use common\components\helpers\HHtml;

return [
    'class'=>'\crud\models\ar\ecommerce\order\models\DeliveryType',
    'config'=>[
        'tablename'=>'ecommerce_order_delivery_types',
        'definitions'=>[
            'column.pk',
            'column.title',
            'column.create_time',
            'column.update_time',
            'column.published',
            'column.sort',
            'is_pickup'=>['type'=>'TINYINT(1) NOT NULL DEFAULT 0', 'label'=>'Самовывоз'],
            'discount'=>['type'=>'DECIMAL(15,2)', 'label'=>'Скидка (%)'],
        ],        
        'rules'=>[
            'safe',
            ['is_pickup', 'boolean'],
            ['discount', 'numerical'],
        ],
        'methods'=>[
            'function isPickUp() { return !!(int)$this->is_pickup; }',
            'function getDiscount() { return (float)$this->discount; }',
        ]
    ],
    'menu'=>[
        'backend'=>['label'=>'Типы доставки']
    ],
    'buttons'=>[
        'create'=>['label'=>'Добавить тип доставки'],
    ],
    'crud'=>[
        'index'=>[
            'url'=>'/cp/crud/index',
            'title'=>'Типы доставки',
            'gridView'=>[
                'dataProvider'=>[
                    'sort'=>['defaultOrder'=>'`sort`, `title`']
                ],
                'emptyText'=>'Типов доставки не найдено',
                'columns'=>[
                    'column.id',
                    [
                        'name'=>'title',
                        'type'=>'column.title',
                    ],
                    [
                        'name'=>'discount',
                        'type'=>'raw',
                        'headerHtmlOptions'=>['style'=>'width:15%;text-align:center'],
                        'htmlOptions'=>['style'=>'text-align:center'],
                        'value'=>function($data) {
                            if((float)$data->discount === 0.00) {
                                return \CHtml::tag('span', ['class'=>'label label-default'], 'нет');
                            }
                            return HHtml::price($data->discount) . ' %';
                        }
                    ],
                    [
                        'name'=>'is_pickup',
                        'type'=>'raw',
                        'headerHtmlOptions'=>['style'=>'width:10%;text-align:center'],
                        'htmlOptions'=>['style'=>'text-align:center'],
                        'value'=>function($data) {
                            if($data->is_pickup) {
                                return \CHtml::tag('span', ['class'=>'label label-success'], 'Да');
                            }
                            else {
                                return \CHtml::tag('span', ['class'=>'label label-danger'], 'Нет');
                            }
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
            'title'=>'Новый тип доставки',
        ],
        'update'=>[
            'url'=>['/cp/crud/update'],
            'title'=>'Редактирование типа доставки',
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
                    'is_pickup'=>'checkbox',
                    'discount'=>[
                        'type'=>'number',
                        'params'=>[
                            'htmlOptions'=>['class'=>'form-control w10 inline', 'step'=>1, 'min'=>0, 'style'=>'margin-right:5px'],
                            'unit'=>'руб.'
                        ]
                    ],
                ];

                return $attributes;
            }
        ]
    ]
];

    
