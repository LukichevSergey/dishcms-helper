<?php
$cfg=[
    'class'=>'\crud\models\ar\ProductSize',
    'tablename'=>'product_sizes',
    'menu.label'=>'Размеры',
    'buttons.create'=>'Добавить размер',
    'crud.index.title'=>'Размеры',
    'crud.create.title'=>'Новый размер',
    'crud.update.title'=>'Редактирование размера',
    'crud.index.emptyText'=>'Размеров не найдено',
    'crud.index.summaryText'=>'Размеры {start} &mdash; {end} из {count}',
];

return [
    'class'=>$cfg['class'],
    'config'=>[
        'tablename'=>$cfg['tablename'],
        'definitions'=>[
            'column.pk',
            'column.title',
            'column.published',
            'column.sort',
        ],
        'rules'=>[
            'safe',
            ['title', 'required'],
        ],
        'methods'=>[            
        ]
    ],
    'menu'=>[
        'backend'=>['label'=>$cfg['menu.label']]
    ],
    'buttons'=>[
        'create'=>['label'=>$cfg['buttons.create']],
    ],
    'crud'=>[
        'index'=>[
            'url'=>'/cp/crud/index',
            'title'=>$cfg['crud.index.title'],
            'gridView'=>[
                'dataProvider'=>[
                    'sort'=>[
                        'defaultOrder'=>'`t`.`sort`, `t`.`title`'
                    ]
                ],
                'emptyText'=>$cfg['crud.index.emptyText'],
                'summaryText'=>$cfg['crud.index.summaryText'],
                'columns'=>[
                    'column.id',
                    'type'=>'column.title',
                    'common.ext.sort',
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
            'url'=>'/cp/crud/create',
            'title'=>$cfg['crud.create.title'],
        ],
        'update'=>[
            'url'=>['/cp/crud/update'],
            'title'=>$cfg['crud.update.title'],
        ],
        'delete'=>[
            'url'=>['/cp/crud/delete'],
        ],
        'form'=>[
            'attributes'=>[
                'published'=>'checkbox',
                'sort'=>[
                    'type'=>'number',
                    'params'=>[
                        'htmlOptions'=>['class'=>'form-control w10', 'step'=>1],
                    ]
                ],
                'title',
            ]
        ]
    ]
];

    