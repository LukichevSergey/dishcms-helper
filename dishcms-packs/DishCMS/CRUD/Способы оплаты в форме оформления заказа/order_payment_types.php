<?php

use common\components\helpers\HHtml;

return [
    'class'=>'\crud\models\ar\OrderPaymentType',
    'config'=>[
        'tablename'=>'order_payment_types',
        'definitions'=>[
            'column.pk',
            'column.title',
            'column.create_time',
            'column.update_time',
            'column.published',
            'column.sort',
        ],
        'rules'=>[
            'safe',
            ['title', 'required'],
        ],        
    ],
    'menu'=>[
        'backend'=>['label'=>'Способы оплаты в форме оформления заказа']
    ],
    'buttons'=>[
        'create'=>['label'=>'Добавить способ оплаты'],
    ],
    'crud'=>[
        'index'=>[
            'url'=>'/cp/crud/index',
            'title'=>'Способы оплаты',
            'gridView'=>[
                'dataProvider'=>[
                    'sort'=>['defaultOrder'=>'`sort`, `title`']
                ],
                'emptyText'=>'Способов оплаты не найдено',
                'columns'=>[
                    'column.id',
                    'column.title',                    
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
            'title'=>'Новый способ оплаты',
        ],
        'update'=>[
            'url'=>['/cp/crud/update'],
            'title'=>'Редактирование способа оплаты',
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
                ];

                return $attributes;
            }
        ]
    ]
];

    