<?php
use extend\modules\booking\components\helpers\HBooking;

/**
 * Модель "Расписание"
 */
return [
    'class'=>'\crud\models\ar\extend\modules\booking\models\Schedule',
    'config'=>[
        'tablename'=>'booking_schedules',
        'definitions'=>[
            'column.pk',
            'year'=>['type'=>'INT(11)', 'label'=>'Год'],
            'month'=>['type'=>'TINYINT(12)', 'label'=>'Месяц'],
            'day'=>['type'=>'TINYINT(31)', 'label'=>'День'],
            'week'=>['type'=>'TINYINT(7)', 'label'=>'День недели'],
            'hour'=>['type'=>'INT(11)', 'label'=>'Час'],
            'minute'=>['type'=>'INT(11)', 'label'=>'Минута'],
            'hour_to'=>['type'=>'INT(11)', 'label'=>'Час окончания сеансов (включительно)'],
            'session_duration'=>['type'=>'INT(11)', 'label'=>'Продолжительность сеанса (минут)'],
            'session_ticket_count'=>['type'=>'INT(11)', 'label'=>'Количество билетов для одного сеанса'],
            'session_ticket_price'=>['type'=>'DECIMAL(15,2)', 'label'=>'Базовая стоимость одного билета'],
            'break_duration'=>['type'=>'INT(11)', 'label'=>'Продолжительность перерыва между сеансами (минут)'],
            'hash'=>['type'=>'VARCHAR(255)', 'label'=>'Хэш расписания'],
            'column.create_time',
            'column.update_time',
            'column.published'
        ],
        'behaviors'=>[
            'scheduleModelBehavior'=>'\extend\modules\booking\behaviors\models\ScheduleBehavior'
        ],        
    ],
    'menu'=>[
        'backend'=>['label'=>'Расписание']
    ],
    'buttons'=>[
        'create'=>['label'=>'Добавить расписание'],
    ],
    'crud'=>[
        'index'=>[
            'url'=>'/cp/crud/index',
            'title'=>'Список расписаний',
            'gridView'=>[
                'dataProvider'=>[
                    'sort'=>['defaultOrder'=>'`published` DESC, `year`, `month`, `week`, `day`, `hour`, `minute`']
                ],
                'summaryText'=>'',
                'columns'=>[
                    [
                        'name'=>'id',
                        'header'=>'#',
                        'headerHtmlOptions'=>['style'=>'width:1%;text-align:center;white-space:nowrap;vertical-align:top'],
                    ],
                    [
                        'name'=>'year',
                        'headerHtmlOptions'=>['style'=>'width:15%;text-align:center;white-space:nowrap;vertical-align:top'],
                        'htmlOptions'=>['style'=>'text-align:center'],
                        'value'=>'$data->year?:"каждый год"'
                    ],
                    [
                        'name'=>'month',
                        'type'=>'raw',
                        'headerHtmlOptions'=>['style'=>'width:15%;text-align:center;white-space:nowrap;vertical-align:top'],
                        'htmlOptions'=>['style'=>'text-align:center'],
                        'value'=>'$data->getMonthLabel()'
                    ],
                    [
                        'name'=>'week',
                        'headerHtmlOptions'=>['style'=>'width:15%;text-align:center;white-space:nowrap;vertical-align:top'],
                        'htmlOptions'=>['style'=>'text-align:center'],
                        'value'=>'$data->getWeekLabel()'
                    ],
                    [
                        'name'=>'day',
                        'headerHtmlOptions'=>['style'=>'width:15%;text-align:center;white-space:nowrap;vertical-align:top'],
                        'htmlOptions'=>['style'=>'text-align:center'],
                        'value'=>'$data->day?:"каждый день"'
                    ],
                    [
                        'name'=>'start_time',
                        'type'=>'raw',
                        'header'=>'Начало',
                        'headerHtmlOptions'=>['style'=>'width:15%;text-align:center;white-space:nowrap;vertical-align:top'],
                        'htmlOptions'=>['style'=>'text-align:center'],
                        'value'=>function($data) {
                            $start=$data->getStartTime();                            
                            $time=$start[0];
                            if($start[1] !== null) {
                                $time.="<div style='font-size:10px;font-weight:bold;margin-top:5px'>до</div>{$start[1]}";
                            }
                            return $time;
                        }
                    ],
                    [
                        'name'=>'session_ticket_count',
                        'headerHtmlOptions'=>['style'=>'width:15%;text-align:center;font-size:11px;vertical-align:top'],
                        'htmlOptions'=>['style'=>'text-align:center'],
                        'value'=>'(int)$data->session_ticket_count'
                    ],
                    [
                        'name'=>'session_ticket_price',
                        'headerHtmlOptions'=>['style'=>'width:15%;text-align:center;font-size:11px;vertical-align:top'],
                        'htmlOptions'=>['style'=>'text-align:center'],
                        'value'=>'(int)$data->session_ticket_price . " руб."'
                    ],
                    [
                        'name'=>'update_time',
                        'type'=>'raw',
                        'header'=>'Обновлено',
                        'headerHtmlOptions'=>['style'=>'width:10%;text-align:center;white-space:nowrap;vertical-align:top'],
                        'htmlOptions'=>['style'=>'text-align:center'],
                        'value'=>'$data->getLastUpdateTime()'
                    ],
                    [
                        'name'=>'published',
                        'header'=>'Активен',
                        'type'=>'common.ext.published',
                        'headerHtmlOptions'=>['style'=>'width:10%;text-align:center;white-space:nowrap;vertical-align:top']
                    ],
                    'crud.buttons'
                ]
            ]
        ],
        'create'=>[
            'scenario'=>'insert',
            'url'=>'/cp/crud/create',
            'title'=>'Новое расписание',
        ],
        'update'=>[
            'url'=>['/cp/crud/update'],
            'title'=>'Редактирование расписания'
        ],
        'delete'=>[
            'url'=>['/cp/crud/delete'],
        ],
        'form'=>[
            'attributes'=>function($model) {
                if(!$model->session_ticket_count) {
                    $model->session_ticket_count=1;
                }
                
                return [
                    'published'=>'checkbox',
                    'session_ticket_count'=>[
                        'type'=>'number',
                        'params'=>[
                            'htmlOptions'=>['class'=>'form-control w10 inline', 'min'=>1, 'step'=>1]
                        ]
                    ],
                    'session_ticket_price'=>[
                        'type'=>'number',
                        'params'=>[
                            'unit'=>' руб.',
                            'htmlOptions'=>['class'=>'form-control w25 inline', 'min'=>0, 'step'=>1]
                        ]
                    ],
                    'year'=>[
                        'type'=>'dropDownList',
                        'params'=>[
                            'data'=>HBooking::getYearRange(),
                            'htmlOptions'=>['class'=>'form-control w50', 'empty'=>'Каждый год']
                        ]
                    ],
                    'month'=>[
                        'type'=>'dropDownList',
                        'params'=>[
                            'data'=>HBooking::monthLabels(), 
                            'htmlOptions'=>['class'=>'form-control w50', 'empty'=>'Каждый месяц']
                        ]
                    ],
                    'week'=>[
                        'type'=>'dropDownList',
                        'params'=>[
                            'data'=>HBooking::weekLabels(),
                            'htmlOptions'=>['class'=>'form-control w50', 'empty'=>'Каждый день']
                        ]
                    ],
                    'day'=>[
                        'type'=>'dropDownList',
                        'params'=>[
                            'data'=>HBooking::getDaysRange(),
                            'htmlOptions'=>['class'=>'form-control w50', 'empty'=>'Каждый день'],
                            'note'=>'Если в этом поле указан день и выбран день недели, то расписание будет активно только для совпадения дня недели и выбранного дня'
                        ]
                    ],
                    'code.html.start_hour_open'=>'<div class="row"><label>Время начала сеансов</label>',
                    'hour'=>[
                        'type'=>'dropDownList',
                        'params'=>[
                            'data'=>HBooking::getHoursRange(),
                            'tagOptions'=>['class'=>'col-md-4'],
                            'htmlOptions'=>['class'=>'form-control w50', 'empty'=>'Каждый час'],
                            'note'=>'Если указан час начала сеансов, но не указан час окончания сеансов, то расписание будет активно только для указанного часа'
                        ]
                    ],
                    'minute'=>[
                        'type'=>'dropDownList',
                        'params'=>[
                            'data'=>HBooking::getMinutesRange(),
                            'tagOptions'=>['class'=>'col-md-4'],
                            'htmlOptions'=>['class'=>'form-control w50']
                        ]
                    ],
                    'code.html.start_hour_close'=>'</div>',
                    'hour_to'=>[
                        'type'=>'dropDownList',
                        'params'=>[
                            'data'=>HBooking::getHoursRange(),
                            'htmlOptions'=>['class'=>'form-control w50', 'empty'=>'Не указан'],
                            'note'=>'Если час указан меньше, чем в поле "Час", то считается что задан обратный интервал, напр. с 22:00 до 8:00'
                                . '<br/>Если указан час окончания сеансов, и указан час начала сеансов как "Каждый час", то данное расписание активно с 0:00 до указанного часа окончания'
                        ]
                    ],
                    'session_duration'=>[
                        'type'=>'dropDownList',
                        'params'=>[
                            'data'=>HBooking::getMinutesRange(5, 5, 59),
                            'htmlOptions'=>['class'=>'form-control w50 inline', 'empty'=>'Все доступное время']
                        ]
                    ],
                    'break_duration'=>[
                        'type'=>'dropDownList',
                        'params'=>[
                            'data'=>HBooking::getMinutesRange(),
                            'htmlOptions'=>['class'=>'form-control w50 inline'],
                            'note'=>'Если указана продолжительноть перерыва, но не указана продолжительность сеанса, то считается, что продолжительность сеанса равна<br/><code>1 час (минус) продолжительность перерыва</code>'                                
                        ]
                    ],
                ];
            }
        ]
    ]
];