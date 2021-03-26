<?php
return [
    'class'=>'\crud\models\ar\accounts\models\Country',
    'relations'=>[
        'accounts_regions'=>[
            'type'=>'belongs_to',
            'attribute'=>'region_id'
        ]
    ],
    'config'=>[
        'tablename'=>'accounts_countries',
        'definitions'=>[
            'column.pk',
            'column.published',
            'column.create_time',
            'column.sort',
            'column.title',
            'foreign.region_id'=>['label'=>'Регион'],
        ],
        'relations'=>[
            'region'=>[\CActiveRecord::BELONGS_TO, '\crud\models\ar\accounts\models\Region', 'region_id']
        ]
    ],
    'buttons'=>[
        'create'=>['label'=>'Добавить страну'],
    ],
    'crud'=>[
        'breadcrumbs'=>[
            'Пользователи'=>'/cp/crud/index?cid=accounts'
        ],
        'index'=>[
            'url'=>'/cp/crud/index',
            'title'=>'Страны',
            'gridView'=>[
                'dataProvider'=>[
                    'sort'=>['defaultOrder'=>'`sort`']
                ],
                'summaryText'=>'Страны {start} - {end} из {count}',
                'columns'=>[
                    'column.id',
                    'column.title',
                    'common.ext.sort',
                    [
                        'name'=>'published',
                        'header'=>'Активен',
                        'type'=>'common.ext.published',
                        'headerHtmlOptions'=>['style'=>'width:10%;text-align:center;white-space:nowrap;']
                    ],
                    'crud.buttons'
                ]
            ]
        ],
        'create'=>[
            'url'=>'/cp/crud/create',
            'title'=>'Новая страна',
        ],
        'update'=>[
            'url'=>'/cp/crud/update',
            'title'=>'Редактировать страну',
        ],
        'delete'=>[
            'url'=>'/cp/crud/delete',
        ],
        'form'=>[
            'attributes'=>[
                'published'=>'checkbox',
                'region_id'=>'foreign.dropdownlist',
                'sort'=>[
                    'type'=>'number',
                    'params'=>['htmlOptions'=>['class'=>'form-control w10']]
                ],
                'title',
            ]
        ]
    ],
];