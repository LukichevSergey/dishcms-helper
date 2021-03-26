<?php
$cfg=[
    'class'=>'\crud\models\ar\ProductTaste',
    'tablename'=>'product_tastes',
    'sort.category'=>'product_tastes',
    'menu.label'=>'Вкусы',
    'buttons.create'=>'Добавить вкус',
    'crud.index.title'=>'Вкусы',
    'crud.create.title'=>'Новый вкус',
    'crud.update.title'=>'Редактирование вкуса',
];

return [
    'class'=>$cfg['class'],
    'config'=>[
        'tablename'=>$cfg['tablename'],
        'definitions'=>[
            'column.pk',
            'column.title',
            'column.published',
        ],
        'rules'=>[
            'safe',
            ['title', 'required'],
        ],
        'behaviors'=>[
            '\common\ext\sort\behaviors\SortBehavior'
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
                ],
                'sortable'=>[
                    'url'=>'/cp/crud/sortableSave',
                    'category'=>$cfg['sort.category']
                ],
                'columns'=>[
                    'column.id',
                    'type'=>'column.title',
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
                'title',
            ]
        ]
    ]
];

    