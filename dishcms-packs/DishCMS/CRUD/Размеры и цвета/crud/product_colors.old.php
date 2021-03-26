<?php
return [
    'class'=>'\crud\models\ar\ProductColor',
    'config'=>[
        'tablename'=>'product_colors',
        'definitions'=>[
            'column.pk',
            'column.title',
            'column.published',
            'color'=>['type'=>'string', 'label'=>'Цвет'],
        ],
        'rules'=>[
            'safe',
            ['title', 'required'],
            ['color', 'safe'],
        ],
        'behaviors'=>[
            '\common\ext\sort\behaviors\SortBehavior'
        ],
        'methods'=>[            
			'public function getHex($default=null){if($this->color){return "#".trim($this->color,"#");}else{return $default;}}'
        ]
    ],
    'menu'=>[
        'backend'=>['label'=>'Цвета']
    ],
    'buttons'=>[
        'create'=>['label'=>'Добавить цвет'],
    ],
    'crud'=>[
        'index'=>[
            'url'=>'/cp/crud/index',
            'title'=>'Цвета',
            'gridView'=>[
                'dataProvider'=>[
                    'criteria'=>[
                        'select'=>'`t`.`id`, `t`.`title`, `t`.`color`, `t`.`published`',
                    ],
                ],
                'sortable'=>[
                    'url'=>'/cp/crud/sortableSave',
                    'category'=>'product_colors'
                ],
                'columns'=>[
                    'column.id',
                    'type'=>'column.title',
                    [
                        'name'=>'color',
                        'header'=>'Цвет',
                        'headerHtmlOptions'=>['style'=>'text-align:center;width:15%'],
                        'htmlOptions'=>['style'=>'text-align:center;'],
                        'type'=>'raw',
                        'value'=>'($data->color ? ("<span class=\"label\" style=\"background-color:#".$data->color." !important\">&nbsp;&nbsp;&nbsp;</span>") : "")'
                    ],
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
            'title'=>'Новый цвет',
        ],
        'update'=>[
            'url'=>['/cp/crud/update'],
            'title'=>'Редактирование цвета',
        ],
        'delete'=>[
            'url'=>['/cp/crud/delete'],
        ],
        'form'=>[
            'attributes'=>[
                'published'=>'checkbox',
                'title',
                'color'=>'colorPicker',
            ]
        ]
    ]
];

    
