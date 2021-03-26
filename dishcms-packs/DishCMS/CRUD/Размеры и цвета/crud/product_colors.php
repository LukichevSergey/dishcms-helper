<?php
$cfg=[
    'class'=>'\crud\models\ar\ProductColor',
    'tablename'=>'product_colors',
    'attribute.color.label'=>'Цвет',
    'menu.label'=>'Цвета',
    'buttons.create'=>'Добавить цвет',
    'crud.index.title'=>'Цвета',
    'crud.create.title'=>'Новый цвет',
    'crud.update.title'=>'Редактирование цвета',
    'crud.index.emptyText'=>'Цветов не найдено',
    'crud.index.summaryText'=>'Цвета {start} &mdash; {end} из {count}',
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
            'color'=>['type'=>'string', 'label'=>$cfg['attribute.color.label']],
        ],
        'rules'=>[
            'safe',
            ['title', 'required'],
            ['color', 'safe'],
        ],
        'methods'=>[     
            'public function getHex($default=null){if($this->color){return "#".trim($this->color,"#");}else{return $default;}}'       
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
                    [
                        'name'=>'color',
                        'headerHtmlOptions'=>['style'=>'text-align:center;width:15%'],
                        'htmlOptions'=>['style'=>'text-align:center;'],
                        'type'=>'raw',
                        'value'=>'($data->color ? ("<span class=\"label\" style=\"background-color:#".$data->color." !important\">&nbsp;&nbsp;&nbsp;</span>") : "")'
                    ],
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
                'color'=>'colorPicker',
            ]
        ]
    ]
];

    