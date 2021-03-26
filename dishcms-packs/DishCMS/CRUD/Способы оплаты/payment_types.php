<?php

use common\components\helpers\HHtml;

return [
    'class'=>'\crud\models\ar\PaymentType',
    'config'=>[
        'tablename'=>'payment_types',
        'definitions'=>[
            'column.pk',
            'column.image',
            'column.title',
            'column.create_time',
            'column.update_time',
            'column.published',
            'column.sort',
            'show_in_footer'=>['type'=>'TINYINT(1) NOT NULL DEFAULT 0', 'label'=>'Показывать в подвале сайта'],
            'show_only_in_footer'=>['type'=>'TINYINT(1) NOT NULL DEFAULT 0', 'label'=>'Показывать ТОЛЬКО в подвале сайта'],
        ],
        'rules'=>[
            'safe',
            ['show_in_footer, show_only_in_footer', 'boolean']
        ],
        'scopes'=>[
            'inFooter'=>['condition'=>'`show_in_footer`=1 OR `show_only_in_footer`=1', 'order'=>'`sort`, `title`'],
	    'notOnlyFooter'=>['condition'=>'`show_only_in_footer`!=1', 'order'=>'`sort`, `title`'],
        ]
    ],
    'menu'=>[
        'backend'=>['label'=>'Способы оплаты']
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
                        'name'=>'title',
                        'type'=>'column.title',
                        /*
                        'info'=>[
                            'Показывать в подвале сайта'=>'$data->show_in_footer ? "да" : "нет"',
                            'Показывать ТОЛЬКО в подвале сайта'=>'$data->show_only_in_footer ? "да" : "нет"',
                        ]
                        */
                    ],
                    [
                        'name'=>'show_in_footer',
                        'type'=>'raw',
                        'header'=>'Подвал',
                        'headerHtmlOptions'=>['style'=>'width:10%;text-align:center', 'title'=>'Показывать в подвале сайта'],
                        'htmlOptions'=>['style'=>'text-align:center'],
                        'value'=>function($data) {
                            if($data->show_in_footer) {
                                return \CHtml::tag('span', ['class'=>'label label-success'], 'Да');
                            }
                            else {
                                return \CHtml::tag('span', ['class'=>'label label-danger'], 'Нет');
                            }
                        }
                    ],                 
                    [
                        'name'=>'show_only_in_footer',
                        'type'=>'raw',
                        'header'=>'!Подвал',
                        'headerHtmlOptions'=>['style'=>'width:10%;text-align:center', 'title'=>'Показывать ТОЛЬКО в подвале сайта'],
                        'htmlOptions'=>['style'=>'text-align:center'],
                        'value'=>function($data) {
                            if($data->show_only_in_footer) {
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
            'htmlOptions'=>['enctype'=>'multipart/form-data'],
            'attributes'=>function($model) {
                $attributes=[
                    'published'=>'checkbox',
                    'show_in_footer'=>'checkbox',
                    'show_only_in_footer'=>'checkbox',
                    'sort'=>[
                        'type'=>'number',
                        'params'=>[
                            'htmlOptions'=>['class'=>'form-control w10', 'step'=>1],
                        ]
                    ],
                    'title',
                    'image'=>'common.ext.file.image',
                ];

                return $attributes;
            }
        ]
    ]
];

    
