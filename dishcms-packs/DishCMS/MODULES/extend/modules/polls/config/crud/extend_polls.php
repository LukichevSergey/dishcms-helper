<?php
/**
 * Опросы и голосования
 *
 */
use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HDb;
use extend\modules\polls\PollsModule;

Y::loadModule('extend.polls');
$t=PollsModule::t('crud');

return [
    'class'=>'\crud\models\ar\extend\modules\polls\models\Poll',
    'relations'=>[
        'extend_polls_questions'=>[
            'type'=>'has_many',
            'attribute'=>'poll_id'
        ]
    ],
    'access'=>[
        ['allow', 'users'=>['@'], 'roles'=>['admin', 'sadmin', 'crud_extend_polls_moderator']],
        ['deny', 'users'=>['*']],
    ],
    'config'=>[
        'tablename'=>'extend_polls',
        'definitions'=>[
            'column.pk',
            'column.create_time',
            'column.update_time',
            'column.published',
            'column.title'=>['label'=>$t('polls.attribute.title')],
            'sort'=>['type'=>'INT(11), KEY(`sort`)', 'label'=>$t('polls.attribute.sort')],
            'finish_time'=>['type'=>'DATETIME', 'label'=>$t('polls.attribute.finish_time')],
            'text'=>['type'=>'TEXT', 'label'=>$t('polls.attribute.text')],
        ],
        'consts'=>[
            'ROLE_MODERATOR'=>'crud_extend_polls_moderator'
        ],
        'behaviors'=>[
            'pollModelBehavior'=>'\extend\modules\polls\behaviors\PollModelBehavior',
        ],
        'relations'=>[
            'questions'=>[\CActiveRecord::HAS_MANY, '\crud\models\ar\extend\modules\polls\models\Question', 'poll_id']
        ]
    ],
    'public'=>[
        'access'=>[
            ['allow', 'users'=>['*'], 'actions'=>['add', 'ajax']],
        ],
        'controllers'=>[
            '\extend\modules\polls\behaviors\PollAjaxControllerBehavior'
        ],
    ],
    'menu'=>[
        'backend'=>['label'=>$t('polls.menu.backend.label')]
    ],
    'buttons'=>[
        'create'=>['label'=>$t('polls.buttons.create.label')],
        'custom'=>function() {
            return \CHtml::link('Журнал', '/cp/crud/index?cid=extend_polls_results', ['class'=>'btn btn-warning pull-right']);
        }
    ],
    'crud'=>[
        'controllers'=>[
            '\extend\modules\polls\behaviors\PollCrudAjaxControllerBehavior'
        ],        
        'index'=>[
            'url'=>'/cp/crud/index',
            'title'=>$t('polls.crud.index.title'),
            'gridView'=>[
                'id'=>'crudPollsGridViewId',
                'dataProvider'=>[
                    'criteria'=>[],
                    'sort'=>['defaultOrder'=>'create_time DESC, id DESC'],
                ],
                'columns'=>[
                    'column.id',
                    [
                        'type'=>'column.relation.extend_polls_questions',
                        'header'=>$t('polls.crud.index.gridView.columns.title.header'),
                        'headerHtmlOptions'=>['style'=>'width:70%;'],
                        'info'=>[
                            $t('polls.crud.index.gridView.columns.title.info.text')=>'$data->text',
                        ]                        
                    ],
                    [
                        'name'=>'sort',
                        'header'=>$t('polls.crud.index.gridView.columns.sort.header'),
                        'headerHtmlOptions'=>['style'=>'width:5%;text-align:center;white-space:nowrap;'],
                        'htmlOptions'=>['style'=>'text-align:center'],
                    ],
                    [
                        'name'=>'create_time',
                        'header'=>$t('polls.crud.index.gridView.columns.create_time.header'),
                        'headerHtmlOptions'=>['style'=>'width:10%;text-align:center'],
                        'htmlOptions'=>['style'=>'text-align:center'],
                    ],
                    /* [
                        'name'=>'finish_time',
                        'type'=>'raw',
                        'header'=>$t('polls.crud.index.gridView.columns.finish_time.header'),
                        'headerHtmlOptions'=>['style'=>'width:10%;text-align:center', 'title'=>$t('polls.crud.index.gridView.columns.finish_time.header.title')],
                        'htmlOptions'=>['style'=>'text-align:center'],
                        'value'=>'(\common\components\helpers\HTools::isDateEmpty($data->finish_time) ? "'.$t('polls.crud.index.gridView.columns.finish_time.empty').'" : $data->finish_time)'
                    ], */
                    [
                        'name'=>'published',
                        'header'=>'Опубл.',
                        'headerHtmlOptions'=>['style'=>'width:5%;text-align:center;white-space:nowrap;'],
                        'type'=>'common.ext.published'
                    ],
                    'crud.buttons'=>[
                        'type'=>'crud.buttons',
                        'params'=>[
                            'template'=>'{extend_polls_questions_items}{update}{delete}',
                            'buttons'=>[
                                'extend_polls_questions_items'=>[
                                    'label'=>'<span class="glyphicon glyphicon-list-alt"></span> Вопросы',
                                    'options'=>['class'=>'btn btn-xs btn-warning w100', 'style'=>'margin-top:2px']
                                ],
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
            'scenario'=>'insert',
            'url'=>'/cp/crud/create',
            'title'=>$t('polls.crud.create.title'),
        ],
        'update'=>[
            'url'=>['/cp/crud/update'],
            'title'=>$t('polls.crud.update.title'),
        ],
        'delete'=>[
            'url'=>['/cp/crud/delete'],
        ],
        'form'=>[
            // 'htmlOptions'=>['enctype'=>'multipart/form-data'],
            'attributes'=>[
                'published'=>'checkbox',
                'sort'=>[
                    'type'=>'number',
                    'params'=>['htmlOptions'=>['class'=>'form-control w10', 'step'=>1]]
                ],
                'title',
                /* 'finish_time'=>[
                    'type'=>'dateTime',
                    'params'=>[
                        'options'=>[
                            'dateFormat'=>'yy-mm-dd',
                            'timeFormat'=>'hh:mm:ss',
                            'changeMonth'=>true,
                            'changeYear'=>true,
                            'minDate'=>0,
                            'hourMax'=>24,
                            'minuteMax'=>60
                        ]
                    ]
                ], 
                'code.html'=>'<button class="btn btn-xs btn-primary" onclick="$(\'#crud_models_ar_extend_modules_polls_models_Poll_finish_time\').val(\'0000-00-00 00:00:00\');return false;">Установить бессрочную дату и время завершения</button>',
                */ 
                'text'=>[
                    'type'=>'tinyMce',
                    'params'=>['full'=>false]
                ]
            ]
            /**/
        ],
        /*
        'tabs'=>[
            'main'=>[
                'title'=>$t('polls.tabs.main.title'),
                'attributes'=>[
                    'published'=>'checkbox',
                    'sort'=>[
                        'type'=>'number',
                        'params'=>['htmlOptions'=>['class'=>'form-control w10', 'step'=>1]]
                    ],
                    'title',
                    'finish_time'=>[
                        'type'=>'dateTime',
                        'params'=>[
                            'options'=>[
                                'dateFormat'=>'yy-mm-dd',
                                'timeFormat'=>'hh:mm:ss',
                                'changeMonth'=>true,
                                'changeYear'=>true,
                                'minDate'=>0,
                                'hourMax'=>24,
                                'minuteMax'=>60
                            ]
                        ]
                    ],
                    'code.html'=>'<button class="btn btn-xs btn-primary" onclick="$(\'#crud_models_ar_extend_modules_polls_models_Poll_finish_time\').val(\'0000-00-00 00:00:00\');return false;">Установить бессрочную дату и время завершения</button>',
                    'text'=>[
                        'type'=>'tinyMce',
                        'params'=>['full'=>false]
                    ]
                ]
            ],
            'questions'=>[
                'title'=>$t('polls.tabs.questions.title'),
                'attributes'=>function(&$model) use ($t) {
                    Y::js(false, 
'window.crudExtendPollsQuestionIdx=0;
window.crudExtendPollsAddQuestion=function(){
    $.post("/common/crud/admin/default/ajax", {action:"getQuestionForm", cid:"extend_polls", idx:window.crudExtendPollsQuestionIdx}, function(r){
        if(r.success) {
            $(".js-polls__questions").append(r.data.html);
            window.crudExtendPollsQuestionIdx++;
        }
    }, "json");
    
}', \CClientScript::POS_READY);
                    return [
                        'code.html'=>'<button class="btn btn-primary" onclick="window.crudExtendPollsAddQuestion();return false;">Добавить вопрос</button>',
                        'code.html.questions'=>'<div class="js-polls__questions"></div>'
                    ];
                }
            ]            
        ]
        */
    ]
];