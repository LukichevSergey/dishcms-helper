<?php
/**
 * Модель "Заявки бронирования"
 */
use common\components\helpers\HArray as A;

return [
    'class'=>'\crud\models\ar\extend\modules\booking\models\Request',
    'config'=>[
        'tablename'=>'booking_requests',
        'definitions'=>[
            'column.pk',
            'date'=>['type'=>'DATETIME', 'label'=>'Дата и время бронирования'],
            'count'=>['type'=>'INT(11)', 'label'=>'Количество человек'],
            'name'=>['type'=>'string', 'label'=>'Имя'],
            'phone'=>['type'=>'string', 'label'=>'Номер телефона'],
            'comment'=>['type'=>'string', 'label'=>'Сообщение'],
            'price'=>['type'=>'DECIMAL(15,2)', 'label'=>'Стоимость одного билета'],
            'column.published'=>['name'=>'reject', 'label'=>'Отменено'],
            'column.create_time',
            'column.update_time',
        ],
        'behaviors'=>[
            'requestModelBehavior'=>'\extend\modules\booking\behaviors\models\RequestBehavior'
        ]
    ],
    'menu'=>[
        'backend'=>['label'=>'Заявки бронирования']
    ],
    'buttons'=>[
        'create'=>['label'=>A::existsKey($_REQUEST, 'archive') ? '' : 'Добавить бронь'],
        'custom'=>function() {
            if(A::existsKey($_REQUEST, 'archive')) {
                return \CHtml::link('Вернуться к активным заявкам', '?cid=booking_requests', ['class'=>'btn btn-info']);
            }
            else {
                return \CHtml::link('Архив', '?cid=booking_requests&archive', ['class'=>'btn btn-info pull-right']);
            }
        }
    ],
    'crud'=>[
        'index'=>[
            'url'=>'/cp/crud/index',
            'title'=>A::existsKey($_REQUEST, 'archive') ? 'Архив заявок бронирования' : 'Заявки бронирования',
            'gridView'=>[
                'dataProvider'=>call_user_func(function() {
                    $dateConditionOperator=A::existsKey($_REQUEST, 'archive') ? '<' : '>=';
                    return [
                        'criteria'=>['condition'=>"DATE(`date`) {$dateConditionOperator} CURDATE()"],
                        'sort'=>['defaultOrder'=>'`date` ASC, `create_time` DESC, `id` DESC'],
                    ];
                }),
                'summaryText'=>'',
                'columns'=>[
                    [
                        'name'=>'id',
                        'header'=>'#',
                        'headerHtmlOptions'=>['style'=>'width:1%;text-align:center;white-space:nowrap;vertical-align:top'],
                    ],
                    [
                        'type'=>'column.title',
                        'name'=>'name',
                        'header'=>'Контактная информация',
                        'attributeTitle'=>'name',
                        'info'=>[
                            'Статус'=>'$data->reject ? \CHtml::tag("span",["class"=>"label label-danger"],"отменено") : ""',
                            'Телефон'=>'$data->phone',
                            'Сообщение'=>'$data->comment',
                        ],
                        'params'=>[
                            'headerHtmlOptions'=>['style'=>'width:40%;text-align:center;white-space:nowrap;vertical-align:top'],
                        ]
                    ],
                    [
                        'type'=>'raw',
                        'name'=>'date',
                        'headerHtmlOptions'=>['style'=>'width:15%;text-align:center;vertical-align:top'],
                        'htmlOptions'=>['style'=>'text-align:center'],
                        'value'=>'preg_replace("/\s+в/", "<br/>", $data->getFormattedDate())'
                    ],
                    [
                        'name'=>'count',
                        'headerHtmlOptions'=>['style'=>'width:10%;text-align:center;vertical-align:top'],
                        'htmlOptions'=>['style'=>'text-align:center'],
                    ],                    
                    [
                        'name'=>'price',
                        'header'=>'Сумма',
                        'headerHtmlOptions'=>['style'=>'width:15%;text-align:center;white-space:nowrap;vertical-align:top'],
                        'htmlOptions'=>['style'=>'text-align:center'],
                        'value'=>'\common\components\helpers\HHtml::price((int)$data->count * (float)$data->price) . " руб."'
                    ],
                    [
                        'name'=>'create_time',
                        'header'=>'Создано',
                        'headerHtmlOptions'=>['style'=>'width:10%;text-align:center;white-space:nowrap;vertical-align:top'],
                        'htmlOptions'=>['style'=>'text-align:center'],
                    ],
                    'crud.buttons'
                ]
            ]
        ],
        'create'=>[
            'scenario'=>'insert',
            'url'=>'/cp/crud/create',
            'title'=>'Новая бронь',
        ],
        'update'=>[
            'url'=>['/cp/crud/update'],
            'title'=>'Редактирование брони'
        ],
        'delete'=>[
            'url'=>['/cp/crud/delete'],
        ],
        'form'=>[
            'attributes'=>[
                'name',
                'phone'=>'phone',
                'date'=>[
                    'type'=>'dateTime',
                    'params'=>[
                        'options'=>['dateFormat'=>'yy-mm-dd', 'timeFormat'=>'hh:mm:ss', 'hourMax'=>24, 'minuteMax'=>59, 'stepMinute'=>5]
                    ]
                ],
                'count'=>[
                    'type'=>'number',
                    'params'=>[
                        'htmlOptions'=>['class'=>'form-control w10 inline', 'min'=>1, 'step'=>1]
                    ]
                ],
                'price'=>[
                    'type'=>'number',
                    'params'=>[
                        'unit'=>' руб.',
                        'htmlOptions'=>['class'=>'form-control w25 inline', 'min'=>0, 'step'=>1]
                    ]
                ],
                'comment'=>'textArea',
                'reject'=>[
                    'type'=>'checkbox',
                    'params'=>[
                        'tagOptions'=>['class'=>'row alert alert-danger']
                    ]
                ]
            ]
       ]
    ]
];