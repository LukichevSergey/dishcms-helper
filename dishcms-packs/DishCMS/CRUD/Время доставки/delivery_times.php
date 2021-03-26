<?php

use common\components\helpers\HHtml;

return [
    'class'=>'\crud\models\ar\DeliveryTime',
    'config'=>[
        'tablename'=>'delivery_times',
        'definitions'=>[
            'column.pk',
            'column.title',
            'column.create_time',
            'column.update_time',
            'column.published',
            'column.sort',
            'time_from_h'=>['type'=>'TINYINT(24) NOT NULL DEFAULT 0', 'label'=>'Время от (часы)'],
            'time_from_m'=>['type'=>'TINYINT(59) NOT NULL DEFAULT 0', 'label'=>'Время от (минуты)'],
            'time_to_h'=>['type'=>'TINYINT(24) NOT NULL DEFAULT 0', 'label'=>'Время до (часы)'],
            'time_to_m'=>['type'=>'TINYINT(59) NOT NULL DEFAULT 0', 'label'=>'Время до (минуты)'],
            'discount'=>['type'=>'DECIMAL(15,2)', 'label'=>'Наценка'],
            'discount_type'=>['type'=>'TINYINT(4)', 'label'=>'Тип наценки'],
        ],
        'rules'=>[
            'safe',
            ['title, time_from_h, time_from_m, time_to_h, time_to_m', 'required'],
            ['time_from_h', 'validateTime'],
            ['discount', 'numerical', 'min'=>0],
            ['discount_type', 'numerical', 'integerOnly'=>true],
        ],
        'consts'=>[
            'DISCOUNT_TYPE_RUB'=>1,
            'DISCOUNT_TYPE_PERCENT'=>2
        ],
        'methods'=>[
            'public function validateTime($attribute, $params) {
                if($this->time_from_h > $this->time_to_h) {
                    $this->addError("time_from_h", "Время доставки задано неверно");
                }
                elseif($this->time_from_h === $this->time_to_h) {
                    if($this->time_from_m > $this->time_to_m) {
                        $this->addError("time_from_h", "Время доставки задано неверно");
                    }
                }
            }',
            'public function getFullTitle() {
                return $this->title . sprintf(\' (с %02d:%02d до %02d:%02d)\', $this->time_from_h, $this->time_from_m, $this->time_to_h, $this->time_to_m);
            }',
            'public function getPrice($orderTotalPrice) {
                switch((int)$this->discount_type) {
                    case self::DISCOUNT_TYPE_RUB:
                        return (float)$this->discount;
                    break;
                    case self::DISCOUNT_TYPE_PERCENT:
                        if((float)$this->discount !== 0.00) {
                            return ($orderTotalPrice / 100) * (float)$this->discount;
                        }
                    break;
                }
                return 0;
            }'
        ]
    ],
    'menu'=>[
        'backend'=>['label'=>'Время доставки']
    ],
    'buttons'=>[
        'create'=>['label'=>'Добавить время'],
    ],
    'crud'=>[
        'index'=>[
            'url'=>'/cp/crud/index',
            'title'=>'Время доставки',
            'gridView'=>[
                'dataProvider'=>[
                    'sort'=>['defaultOrder'=>'`sort`, `id`']
                ],
                'emptyText'=>'Интервалов не найдено',
                'columns'=>[
                    'column.id',
                    'column.title',
                    [
                        'name'=>'time_from',
                        'header'=>'Время доставки',
                        'headerHtmlOptions'=>['style'=>'width:18%;text-align:center'],
                        'htmlOptions'=>['style'=>'text-align:center'],
                        'value'=>function($data) {
                            return sprintf('с %02d:%02d до %02d:%02d', $data->time_from_h, $data->time_from_m, $data->time_to_h, $data->time_to_m);
                        }
                    ],
                    [
                        'name'=>'discount',
                        'type'=>'raw',
                        'headerHtmlOptions'=>['style'=>'width:18%;text-align:center'],
                        'htmlOptions'=>['style'=>'text-align:center'],
                        'value'=>function($data) {
                            if((float)$data->discount === 0.00) {
                                return 'нет';
                            }
                            else {
                                switch((int)$data->discount_type) {
                                    case $data::DISCOUNT_TYPE_RUB:
                                        $unit=' руб.';
                                        break;
                                    case $data::DISCOUNT_TYPE_PERCENT:
                                        $unit=' %';
                                        break;
                                    default:
                                        return \CHtml::tag('span', ['class'=>'label label-danger'], 'ошибка');
                                        break;
                                }
                                if((float)$data->discount > 0) {
                                    return \CHtml::tag('span', ['class'=>'label label-' . (trim($unit)=='%'?'primary':'info')], '+' . HHtml::price($data->discount) . $unit);
                                }
                                else {
                                    return \CHtml::tag('span', ['class'=>'label label-success'], HHtml::price($data->discount) . $unit);
                                }
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
            'title'=>'Новое время доставки',
        ],
        'update'=>[
            'url'=>['/cp/crud/update'],
            'title'=>'Редактирование времени доставки',
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

                    'code.html.timefrom_begin'=>'<div class="row"><label>Время доставки <code>начало</code></label>'
                        . \CHtml::error($model, 'time_from_h'),
                    'code.html.timefrom_h_begin'=>'<div class="col-md-2"><label>Часы</label>',
                    'time_from_h'=>[
                        'type'=>'dropDownList',
                        'params'=>[
                            'data'=>array_map(function($v){
                                return sprintf("%02d", $v);
                            }, array_combine(range(0,23), range(0,23))),
                            'tag'=>false,
                            'htmlOptions'=>['class'=>'form-control w100 inline'],
                            'hideLabel'=>true,
                            'hideError'=>null
                        ]
                    ],
                    'code.html.timefrom_h_end'=>'</div>',
                    'code.html.timefrom_m_begin'=>'<div class="col-md-2"><label>Минуты</label>',
                    'time_from_m'=>[
                        'type'=>'dropDownList',
                        'params'=>[
                            'data'=>array_map(function($v){
                                return sprintf("%02d", $v);
                            }, array_combine(range(0,59, 5), range(0,59, 5))),
                            'tag'=>false,
                            'htmlOptions'=>['class'=>'form-control w100 inline'],
                            'hideLabel'=>true
                        ]
                    ],
                    'code.html.timefrom_m_end'=>'</div>',
                    'code.html.timefrom_end'=>'</div>',

                    'code.html.timeto_begin'=>'<div class="row"><label>Время доставки <code>окончание</code></label>',
                    'code.html.timeto_h_begin'=>'<div class="col-md-2"><label>Часы</label>',
                    'time_to_h'=>[
                        'type'=>'dropDownList',
                        'params'=>[
                            'data'=>array_map(function($v){
                                return sprintf("%02d", $v);
                            }, array_combine(range(0,23), range(0,23))),
                            'tag'=>false,
                            'htmlOptions'=>['class'=>'form-control w100 inline'],
                            'hideLabel'=>true
                        ]
                    ],
                    'code.html.timeto_h_end'=>'</div>',
                    'code.html.timeto_m_begin'=>'<div class="col-md-2"><label>Минуты</label>',
                    'time_to_m'=>[
                        'type'=>'dropDownList',
                        'params'=>[
                            'data'=>array_map(function($v){
                                return sprintf("%02d", $v);
                            }, array_combine(range(0,59, 5), range(0,59, 5))),
                            'tag'=>false,
                            'htmlOptions'=>['class'=>'form-control w100 inline'],
                            'hideLabel'=>true
                        ]
                    ],
                    'code.html.timeto_m_end'=>'</div>',
                    'code.html.timeto_end'=>'</div>',

                    'code.html.discount_begin'=>'<div class="row"><label>Наценка</label>',
                    // <p class="note">Можно указать отрицательное число, если требуется предоставить скидку</p>',
                    'code.html.discount_h_begin'=>'<div class="col-md-2"><label>Значение</label>',
                    'discount'=>[
                        'type'=>'number',
                        'params'=>[
                            'htmlOptions'=>['class'=>'form-control w100 inline', 'step'=>0.01, 'min'=>0],
                            'tag'=>false,
                            'hideLabel'=>true
                        ]
                    ],
                    'code.html.discount_h_end'=>'</div>',
                    'code.html.discount_m_begin'=>'<div class="col-md-2"><label>Тип</label>',
                    'discount_type'=>[
                        'type'=>'dropDownList',
                        'params'=>[
                            'data'=>[
                                $model::DISCOUNT_TYPE_RUB=>'руб',
                                $model::DISCOUNT_TYPE_PERCENT=>'%'
                            ],
                            'tag'=>false,
                            'htmlOptions'=>['class'=>'form-control w100 inline'],
                            'hideLabel'=>true
                        ]
                    ],
                    'code.html.discount_m_end'=>'</div>',
                    'code.html.discount_end'=>'</div>',
                ];

                return $attributes;
            }
        ]
    ]
];

    
