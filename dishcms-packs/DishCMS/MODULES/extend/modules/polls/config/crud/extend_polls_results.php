<?php
/**
 * Опросы и голосования. Голос.
 *
 */
use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HDb;
use extend\modules\polls\PollsModule;

Y::loadModule('extend.polls');
$t=PollsModule::t('crud');

return [
    'class'=>'\crud\models\ar\extend\modules\polls\models\Result',
    'access'=>[
        ['allow', 'users'=>['@'], 'roles'=>['admin', 'sadmin', 'extend_polls_moderator'], 'actions'=>'index, delete'],
        ['deny', 'users'=>['*']],
    ],
    'config'=>[
        'tablename'=>'extend_polls_results',
        'definitions'=>[
            'column.pk',
            'ip'=>'VARCHAR(128)',
            'foreign.poll_id'=>['label'=>'Опрос'],
            'foreign.question_id'=>['label'=>'Вопрос'],
            'answer_hash'=>['type'=>'BIGINT, KEY(`answer_hash`)', 'label'=>'Ответ'],
            'user_hash'=>'BIGINT, KEY(`user_hash`)',
            'result_hash'=>['type'=>'BIGINT, KEY(`result_hash`)', 'label'=>'Общий идентификатор результата опроса'],
            'column.create_time',
        ],
        'consts'=>[
            'ROLE_MODERATOR'=>'extend_polls_moderator'
        ],
        'behaviors'=>[
            'resultModelBehavior'=>'\extend\modules\polls\behaviors\ResultModelBehavior'
        ],
        'relations'=>[
            'poll'=>[\CActiveRecord::BELONGS_TO, '\crud\models\ar\extend\modules\polls\models\Poll', 'poll_id'],
            'question'=>[\CActiveRecord::BELONGS_TO, '\crud\models\ar\extend\modules\polls\models\Question', 'question_id']
        ]
    ],
    'buttons'=>[
        'create'=>['label'=>''],
        'custom'=>function() use ($t) {
            return \CHtml::link($t('votes.buttons.custom.btn.back'), '/cp/crud/index?cid=extend_polls', ['class'=>'btn btn-default']);
        }
    ],
    'crud'=>[
        'index'=>[
            'url'=>'/cp/crud/index',
            'title'=>'Результаты',
            'breadcrumbs'=>[
                $t('polls.crud.index.title')=>['/cp/crud/index', 'cid'=>'extend_polls']
            ],
            'gridView'=>[
                'id'=>'crudPollsResultsGridViewId',
                'dataProvider'=>[
                    'criteria'=>[
                        'select'=>'id, poll_id, ip, result_hash, create_time',
                        'group'=>'result_hash'                        
                    ],
                    'sort'=>['defaultOrder'=>'create_time DESC, id DESC'],
                ],
                'columns'=>[
                    [
                        'name'=>'ip',
                        'header'=>'IP',
                        'type'=>'raw',
                        'value'=>'"<small><b>IP:</b> {$data->ip}<br/><b>ID:</b> {$data->result_hash}</small>"'
                    ],
                    [
                        'name'=>'result',
                        'header'=>'Результаты',
                        'type'=>'raw',
                        'value'=>'\common\components\helpers\HYii::controller()->renderPartial("extend.modules.polls.views.crud._result_item_info", compact("data"), true)'
                    ],
                    /* 
                    'column.id',
                    [
                        'name'=>'info',
                        'header'=>'Информация',
                        'type'=>'raw',
                        'value'=>'"<small>"'
                            . '. "<b>Опрос:</b> " . (($poll=$data->getRelated("poll")) ? $poll->title : "ID #{$data->poll_id}") . "<br/>"'
                            . '. "<b>Вопрос:</b> " . (($question=$data->getRelated("question")) ? $question->title : "ID #{$data->question_id}") . "<br/>"'
                            . '. "<b>Ответ:</b> " . (($title=$question->answersBehavior->find("hash", $data->answer_hash, ["v"=>"title"])) ? $title : "HASH #{$data->answer_hash}") . "<br/>"'
                            . '. "</small>"'
                    ],
                    /**/
                    [
                        'name'=>'create_time',
                        'header'=>$t('polls.crud.index.gridView.columns.create_time.header'),
                        'headerHtmlOptions'=>['style'=>'width:10%;text-align:center'],
                        'htmlOptions'=>['style'=>'text-align:center'],
                    ],
                    'crud.buttons'=>[
                        'type'=>'crud.buttons',
                        'params'=>[
                            'template'=>'{delete}',
                            'buttons'=>[
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
        'delete'=>[
            'url'=>['/cp/crud/delete'],
        ],
    ]
];