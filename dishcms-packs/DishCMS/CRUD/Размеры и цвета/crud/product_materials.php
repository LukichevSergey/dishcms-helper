<?php
$cfg=[
    'class'=>'\crud\models\ar\ProductMaterial',
    'tablename'=>'product_materials',
    'menu.label'=>'Материалы',
    'buttons.create'=>'Добавить материал',
    'crud.index.title'=>'Материалы',
    'crud.create.title'=>'Новый материал',
    'crud.update.title'=>'Редактирование материала',    
    'crud.index.emptyText'=>'Материалов не найдено',
    'crud.index.summaryText'=>'Материалы {start} &mdash; {end} из {count}',
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
                    'criteria'=>[
                        'select'=>'`t`.`id`, `t`.`title`, `t`.`published`, `t`.`sort`',
                    ],
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

    