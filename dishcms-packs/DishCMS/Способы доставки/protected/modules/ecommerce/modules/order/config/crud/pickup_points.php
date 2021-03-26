<?php

use common\components\helpers\HHtml;

return [
    'class'=>'\crud\models\ar\ecommerce\order\models\PickupPoint',
    'config'=>[
        'tablename'=>'ecommerce_order_pickup_points',
        'definitions'=>[
            'column.pk',
            'column.title'=>['label'=>'Адрес филиала для самовывоза'],
            'column.create_time',
            'column.update_time',
            'column.published',
            'column.sort',
            'email'=>['type'=>'string', 'label'=>'E-Mail для уведомления о новом заказе'],
        ],        
        'rules'=>[
            'safe',
            ['email', 'email']
        ]
    ],
    'menu'=>[
        'backend'=>['label'=>'Адреса самовывоза']
    ],
    'buttons'=>[
        'create'=>['label'=>'Добавить адрес'],
    ],
    'crud'=>[
        'index'=>[
            'url'=>'/cp/crud/index',
            'title'=>'Адреса филиалов для самовывоза',
            'gridView'=>[
                'dataProvider'=>[
                    'sort'=>['defaultOrder'=>'`sort`, `title`']
                ],
                'emptyText'=>'Адресов не найдено',
                'columns'=>[
                    'column.id',
                    [
                        'name'=>'title',
                        'type'=>'column.title',
                        'header'=>'Адрес филиала для самовывоза',
                        'info'=>[
                            'E-Mail'=>'$data->email'
                        ]
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
            'title'=>'Новый адрес для самовывоза',
        ],
        'update'=>[
            'url'=>['/cp/crud/update'],
            'title'=>'Редактирование адреса для самовывоза',
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
                    'email',
                ];

                return $attributes;
            }
        ]
    ]
];

    
