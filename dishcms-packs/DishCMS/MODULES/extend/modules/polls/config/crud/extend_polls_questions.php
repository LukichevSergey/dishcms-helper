<?php
/**
 * Опросы и голосования
 *
 */
use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use extend\modules\polls\PollsModule;

Y::loadModule('extend.polls');
$t=PollsModule::t('crud');

return [
    'class'=>'\crud\models\ar\extend\modules\polls\models\Question',
    'relations'=>[
        'extend_polls'=>[
            'type'=>'belongs_to',
            'attribute'=>'poll_id'
        ]
    ],
    'access'=>[
        ['allow', 'users'=>['@'], 'roles'=>['admin', 'sadmin', 'crud_extend_polls_moderator']],
        ['deny', 'users'=>['*']],
    ],
    'config'=>[
        'tablename'=>'extend_polls_questions',
        'definitions'=>[
            'column.pk',
            'foreign.poll_id'=>['label'=>'Опрос'],
            'column.title'=>['label'=>'Вопрос'],
            'column.published',
            'required'=>['type'=>'boolean', 'label'=>'Выбор ответа является обязательным'],
            'multiple'=>['type'=>'boolean', 'label'=>'Разрешен выбор нескольких вариантов'],
            'can_other'=>['type'=>'boolean', 'label'=>'Разрешено указывать свой вариант ответа'],
            'sort'=>['type'=>'INT(11), KEY(`sort`)', 'label'=>'Порядок сортировки'],
            'answers'=>['type'=>'LONGTEXT', 'label'=>'Варианты ответа'],
        ],
        'consts'=>[
            'ROLE_MODERATOR'=>'crud_extend_polls_moderator',
            'CLASS_HASH'=>'7cA8DwXxH9tCuLY9'
        ],
        'behaviors'=>[
            'questionModelBehavior'=>'\extend\modules\polls\behaviors\QuestionModelBehavior',
            'answersBehavior'=>[
                'class'=>'\common\ext\dataAttribute\behaviors\DataAttributeBehavior',
                'attribute'=>'answers'
            ]
        ],
        'relations'=>[
            'poll'=>[\CActiveRecord::BELONGS_TO, '\crud\models\ar\extend\modules\polls\models\Poll', 'poll_id']
        ],
    ],
    'public'=>[
        'access'=>[
            ['allow', 'users'=>['*'], 'actions'=>['list', 'vote']],
        ],
    ],
    'menu'=>[
        'backend'=>['label'=>'Вопросы']
    ],
    'buttons'=>[
        'create'=>['label'=>'Добавить вопрос'],
        'custom'=>function() {
            return \CHtml::ajaxLink(
                'Обновить статистику ответов', 
                ['/common/crud/admin/default/ajax', 'cid'=>'extend_polls', 'action'=>'updateStats', 'id'=>A::get($_REQUEST, 'extend_polls')],
                [
                    'beforeSend'=>'js:function(){$(".js-update-stats-btn").button("loading");return true;}',
                    'success'=>'js:function(){$(".js-update-stats-btn").button("reset");$.fn.yiiGridView.update("crudPollsQuestionsGridViewId");}'
                ],
                ['class'=>'btn btn-default pull-right js-update-stats-btn', 'data-loading-text'=>'Идет обновление статистики ответов данного опроса...']
            );
        }
    ],
    'crud'=>[
        /*
        'ajax'=>[
            'form'=>[
                'attributes'=>[
                    'sort'=>[
                        'type'=>'number',
                        'params'=>['htmlOptions'=>['class'=>'form-control w10', 'step'=>1]]
                    ],
                    'title',
                    'required'=>'checkbox',
                    'multiple'=>'checkbox',
                    'can_other'=>'checkbox',
                    'answers'=>[
                        'type'=>'common.ext.data',
                        'behaviorName'=>'answersBehavior',
                        'params'=>[
                            'header'=>[
                                'title'=>'Вариант ответа',
                                'votes'=>['title'=>'Кол-во голосов', 'htmlOptions'=>['style'=>'width:15%;']],
                                'hash'=>['title'=>'', 'htmlOptions'=>['style'=>'width:1px !important;']]
                            ],
                            'readOnly'=>['votes'],
                            'types'=>['hash'=>'hidden'],
                            'default' => [
                                ['title'=>'', 'votes'=>'0', 'hash'=>'']
                            ]
                        ]
                    ],	
                ]
            ]
        ],
        /**/
        'index'=>[
            'url'=>'/cp/crud/index',
            'title'=>'Вопросы',
            'gridView'=>[
                'id'=>'crudPollsQuestionsGridViewId',
                'dataProvider'=>[
                    'criteria'=>[],
                    'sort'=>['defaultOrder'=>'`sort` DESC, `id` ASC'],
                ],
                'columns'=>[
                    'column.id',
                    [
                        'type'=>'column.title',
                        'header'=>'Вопрос',
                        'headerHtmlOptions'=>['style'=>'width:70%;'],
                        'info'=>[
                            'Является обязательным'=>'$data->required ? "<span class=\"label label-success\">Да</span>" : "<span class=\"label label-danger\">Нет</span>"',
                            'Тип'=>'$data->multiple ? "Несколько из" : "Один из"',
                            //'Разрешено указание своего ответа'=>'$data->can_other ? "<span class=\"label label-success\">Да</span>" : "<span class=\"label label-danger\">Нет</span>"',
                            'Варианты ответов'=>'call_user_func(function($data){
                                $html="";$data=$data->answersBehavior->get(true);
                                foreach($data as $i=>$item){$html.="<br/>".($i+1).") {$item[\'title\']}: <b>{$item[\'votes\']}</b> <span style=\"color:#999\">".\common\components\helpers\HTools::pluralLabel((int)$item[\'votes\'], ["голос", "голоса", "голосов"])."</span>";}
                                return $html;}, $data)'
                        ]
                    ],
                    [
                        'name'=>'sort',
                        'header'=>'Сорт.',
                        'headerHtmlOptions'=>['style'=>'width:5%;text-align:center;white-space:nowrap;'],
                        'htmlOptions'=>['style'=>'text-align:center'],
                    ],
                    [
                        'name'=>'published',
                        'header'=>'Опубл.',
                        'headerHtmlOptions'=>['style'=>'width:5%;text-align:center;white-space:nowrap;'],
                        'type'=>'common.ext.published'
                    ],
                    'crud.buttons'=>[
                        'type'=>'crud.buttons',
                        'params'=>[
                            'template'=>'{update}{delete}',
                            'buttons'=>[
                                'update'=>[
                                    'label'=>'<span class="glyphicon glyphicon-pencil"></span> Редактировать',
                                    'options'=>['class'=>'btn btn-xs btn-primary w100', 'style'=>'margin-top:2px']
                                ],
                                'delete'=>[
                                    'label'=>'<span class="glyphicon glyphicon-remove"></span> Удалить',
                                    'options'=>['class'=>'btn btn-xs btn-danger w100', 'style'=>'margin-top:2px']
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ],
        'create'=>[
            'url'=>'/cp/crud/create',
            'title'=>'Новый вопрос',
        ],
        'update'=>[
            'url'=>['/cp/crud/update'],
            'title'=>'Редактирование вопроса',
        ],
        'delete'=>[
            'url'=>['/cp/crud/delete'],
        ],
        'form'=>[
            // 'htmlOptions'=>['enctype'=>'multipart/form-data'],
            'attributes'=>[
                'published'=>'checkbox',
                'poll_id'=>'foreign.readonly',
                'sort'=>[
                    'type'=>'number',
                    'params'=>['htmlOptions'=>['class'=>'form-control w10', 'step'=>1]]
                ],
                'title',
                'required'=>'checkbox',
                'multiple'=>'checkbox',
                //'can_other'=>'checkbox',
                'answers'=>[
                    'type'=>'common.ext.data',
                    'behaviorName'=>'answersBehavior',
                    'params'=>[
                        'header'=>[
                            'title'=>'Вариант ответа',
                            'votes'=>['title'=>'Кол-во голосов', 'htmlOptions'=>['style'=>'width:15%;']],
                            'hash'=>['title'=>'', 'htmlOptions'=>['style'=>'width:1px !important;']]
                        ],
                        'readOnly'=>['votes'],
                        'types'=>['hash'=>'hidden'],
                        'default' => [
                            ['title'=>'', 'votes'=>'0', 'hash'=>'']
                        ]
                    ]
                ]
            ]
        ]
    ]
];