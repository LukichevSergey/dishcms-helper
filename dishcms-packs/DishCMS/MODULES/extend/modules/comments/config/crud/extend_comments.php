<?php
/**
 * Комментарии
 * 
 */
use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HDb;
use extend\modules\comments\components\helpers\HComment;

$t=HComment::t();

return [
    'class'=>'\crud\models\ar\extend\modules\comments\Comment',
    'access'=>[
        ['allow', 'users'=>['@'], 'roles'=>['admin', 'sadmin', 'extend_comments_moderator']],
        ['deny', 'users'=>['*']],
    ],
    'config'=>[
        'tablename'=>'extend_comments',
        'definitions'=>[
            'column.pk',
            'column.create_time',
            'column.update_time',
            'column.published',
            'model'=>['type'=>'VARCHAR(255), KEY(`model`)', 'label'=>$t('crud.comments.attribute.model')],
            'model_id'=>['type'=>'INT(11), KEY(`model_id`)', 'label'=>$t('crud.comments.attribute.model_id')],
            'name'=>['type'=>'string', 'label'=>$t('crud.comments.attribute.name')],
            'rating'=>['type'=>'TINYINT(255) NOT NULL DEFAULT 0', 'label'=>$t('crud.comments.attribute.rating')],
            'comment'=>['type'=>'TEXT', 'label'=>$t('crud.comments.attribute.comment')],
            'data'=>['type'=>'TEXT', 'label'=>$t('crud.comments.attribute.data')],
            'sort'=>['type'=>'INT(11), KEY(`sort`)', 'label'=>$t('crud.comments.attribute.sort')],
        ],
        'behaviors'=>[
            '\common\ext\sort\behaviors\SortBehavior',
            'commentModelBehavior'=>'\extend\modules\comments\behaviors\CommentModelBehavior'
        ],
        'consts'=>[
            'ROLE_MODERATOR'=>'extend_comments_moderator'
        ],
        'methods'=>[
            'public $model_hash;'
        ]
    ],
    'public'=>[
        'access'=>[
            ['allow', 'users'=>['*'], 'actions'=>['list', 'add', 'ajax']],
        ],
        'controllers'=>[
            '\extend\modules\comments\behaviors\CommentAjaxControllerBehavior'
        ],
    ],
    'menu'=>[
        'backend'=>['label'=>$t('crud.comments.menu.backend.label')]
    ],
    'buttons'=>[
        'create'=>['label'=>$t('crud.comments.buttons.create.label')],
        'custom'=>function() {
              $t=HComment::t();
              $model=new \crud\models\ar\extend\modules\comments\Comment;
              $html=\CHtml::openTag('div', ['class'=>'panel panel-default', 'style'=>'margin-top:15px;margin-bottom:0;']);
              //$html.=\CHtml::tag('div', ['class'=>'panel-heading'], $t('crud.comments.buttons.custom.filter.heading'));
              $html.=\CHtml::openTag('div', ['class'=>'panel-body']);
              
              $dropDownListStyle='width:32%;';
              $parentsListData=$model->getParentsListData();
              if(!empty($parentsListData)) {
                  if(count($parentsListData) === 1) {
                      $model->model_hash=key($parentsListData);
                      $model->model=HComment::getConfigByParentHash($model->model_hash)['class'];
                      $html.=\CHtml::hiddenField('js__comment_filter-parents', $model->model_hash, ['id'=>'js__comment_filter-parents']);
                      $dropDownListStyle='width:49%;';
                  }
                  else {
                      $model->model_hash=$model->getParentHash();
                      $html.=\CHtml::dropDownList('js__comment_filter-parents', '', $parentsListData, [
                          'id'=>'js__comment_filter-parents',
                          'class'=>'form-control inline',  
                          'empty'=>$t('crud.comments.buttons.custom.filter.parents.empty'),
                          'style'=>'margin-right:10px;' . $dropDownListStyle
                      ]);
                  }
                  
                  $html.=\CHtml::dropDownList('js__comment_filter-parent', '', $model->getParentListData(), [
                      'id'=>'js__comment_filter-parent',
                      'class'=>'form-control inline', 
                      'style'=>'margin-right:10px;' . $dropDownListStyle,
                      'empty'=>$t('crud.comments.buttons.custom.filter.parent.empty')
                  ]);
              }
              
              $html.=\CHtml::dropDownList('js__comment_filter-model', '', [], [
                  'id'=>'js__comment_filter-model',
                  'class'=>'form-control inline',
                  'style'=>$dropDownListStyle,
                  'empty'=>$t('crud.comments.buttons.custom.filter.model.empty')
              ]);
              $html.=\CHtml::closeTag('div');
              $html.=\CHtml::closeTag('div');
              return $html;
        }
    ],
    'crud'=>[
        'controllers'=>[
            '\extend\modules\comments\behaviors\CommentCrudAjaxControllerBehavior'
        ],
        'index'=>[            
            'url'=>'/cp/crud/index',
            'title'=>$t('crud.comments.crud.index.title'),
            'onBeforeLoad'=>function() {
                $module=Y::module('extend.comments');
                $module->publishAssets();
                $module->publishCss();
                $module->publishJs();
                Y::js('js__extend-comments', ';window.extendCommentsCrud.init();');
            },
            'gridView'=>[
                'id'=>'crudCommentsGridViewId',
                'htmlOptions'=>['style'=>'padding-top:0'],
                'dataProvider'=>[
                    'criteria'=>A::m([
                            'select'=>'`t`.`id`, `t`.`name`, `t`.`rating`, `t`.`comment`, `t`.`published`, `t`.`create_time`, `t`.`data`, `t`.`model`, `t`.`model_id`, `t`.`sort`'
                    ], (empty($_REQUEST['hash']) ? [] : call_user_func(function() {
                            $criteria=[];
                            if($cfg=HComment::getConfigByHash($_REQUEST['hash'])) {
                                $criteria['condition']='`t`.`model`='.HDb::qv($cfg['class']);
                                if(!empty($_REQUEST['parent_id'])) {
                                    if($parentConfig=HComment::getConfigByParentHash($_REQUEST['hash'])) {
                                        $modelClass=$parentConfig['class'];
                                        $criteria['join']='INNER JOIN '.HDb::qt($modelClass::model()->tableName()).' AS `tmodel`'
                                            . ' ON ((`tmodel`.' . HDb::qc($parentConfig['parent']['attributeParentId']) . '=' . (int)$_REQUEST['parent_id'] . ')'
                                            . ' AND (`tmodel`.' . HDb::qc($parentConfig['attributeId']) . '=`t`.`model_id`))';
                                    }
                                    else {
                                        $criteria['condition'].=' AND 1<>1';
                                    }
                                }
                                if(!empty($_REQUEST['model_id'])) {
                                    $criteria['condition'].=' AND `t`.`model_id`='.(int)$_REQUEST['model_id'];
                                }
                            }
                            return $criteria;
                        }))
                    ),
		            'sort'=>['defaultOrder'=>'create_time DESC, id DESC'],
                ],
                'summaryText'=>'',
                'columns'=>[
                    'column.id',                    
                    [
                        'type'=>'column.title',
                        'header'=>$t('crud.comments.crud.index.gridView.columns.title.header'),
                        'headerHtmlOptions'=>['style'=>'width:70%;'],
                        'attributeTitle'=>'name',
                        'info'=>[
                                ':expr:$data->commentModelBehavior->getParentItemLabel()'=>'$data->commentModelBehavior->getParentTitle()',
                                ':expr:$data->commentModelBehavior->getModelItemLabel()'=>'$data->commentModelBehavior->getModelTitle()',
                                $t('crud.comments.crud.index.gridView.columns.title.info.rating')=>'((int)$data->rating ? "<span class=\"star-view star-{$data->rating}\"></span>" : "")',
                                $t('crud.comments.crud.index.gridView.columns.title.info.comment')=>'\CHtml::encode($data->comment)',
                        ]
                    ],
                    [
                        'name'=>'sort',
                        'header'=>$t('crud.comments.crud.index.gridView.columns.title.sort'),
                        'headerHtmlOptions'=>['style'=>'width:5%;text-align:center;white-space:nowrap;'],
                        'htmlOptions'=>['style'=>'text-align:center'],
                    ],
                    [
                        'name'=>'create_time',
                        'header'=>'Дата',
                        'headerHtmlOptions'=>['style'=>'width:10%;text-align:center'],
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
            'scenario'=>'insert',
            'url'=>'/cp/crud/create',
            'title'=>$t('crud.comments.crud.create.title'),
            'onBeforeLoad'=>function() {
                Y::module('extend.comments')->publishJs();
                Y::js('js__extend-comments', ';window.extendCommentsCrud.init();');
            },
        ],
        'update'=>[
            'url'=>['/cp/crud/update'],
            'title'=>$t('crud.comments.crud.update.title'),
            'onBeforeLoad'=>function() {
                Y::module('extend.comments')->publishJs();
                Y::js('js__extend-comments', ';window.extendCommentsCrud.init();');
            },
        ],
        'delete'=>[
            'url'=>['/cp/crud/delete'],
        ],
        'form'=>[
            // 'htmlOptions'=>['enctype'=>'multipart/form-data'],
            'attributes'=>function(&$model) {
                $t=HComment::t();
                
                $attributes = [
                    'published'=>'checkbox',
                    'sort'=>[
                        'type'=>'number',
                        'params'=>['htmlOptions'=>['class'=>'form-control w10', 'step'=>1]]
                    ]
                ];
                
                $parentId=false;
                $parentsListData=$model->getParentsListData();
                if(!empty($parentsListData)) {
                    if(count($parentsListData) === 1) {
                        $hash=key($parentsListData);
                        $model->model_hash=$hash;
                        $attributes['code.html.parents']=\CHtml::activeHiddenField($model, 'model_hash', ['id'=>'js__comment_form-parents']);
                        $cfg=HComment::getConfigByParentHash($hash);
                        $model->model=$cfg['class'];
                    }
                    else {
                        $model->model_hash=$model->getParentHash();
                        $attributes['code.html.parents']=\CHtml::tag('div', ['class'=>'row'],
                            \CHtml::tag('label', ['for'=>'js__comment_form-parents'], $t('crud.comments.form.attributes.parents')) .
                            \CHtml::activeDropDownList(
                                $model,
                                'model_hash',
                                $parentsListData,
                                ['class'=>'form-control w50', 'id'=>'js__comment_form-parents', 'empty'=>$t('crud.comments.form.attributes.parents.empty')]
                            )
                        );
                    }
                    
                    $attributes['code.html.parent']=\CHtml::tag('div', ['class'=>'row'],
                        \CHtml::tag('label', ['for'=>'js__comment_form-parent'], $t('crud.comments.form.attributes.parent')) .
                        \CHtml::dropDownList(
                            'js__comment_form-parent',
                            $model->getParentId(),
                            $model->getParentListData(),
                            ['class'=>'form-control w100', 'empty'=>$t('crud.comments.form.attributes.parent.empty')]
                            )
                        );
                    
                    $parentId=$model->getParentId();
                }
                
                $modelListCriteria=HDb::criteria(($parentId===false) ? [] : ($parentId ? null : ['condition'=>'1<>1']));
                $attributes['model_id']=[
                    'type'=>'dropDownList',
                    'params'=>[
                        'data'=>$model->getModelListData($modelListCriteria),
                        'htmlOptions'=>[
                            'id'=>'js__comment_form-model',
                            'class'=>'form-control w100', 
                            'empty'=>$t('crud.comments.form.attributes.model.empty')
                        ]
                    ]
                ];
                
                return A::m($attributes, [
                    'name',
                    'rating'=>[
                        'type'=>'number',
                        'params'=>['htmlOptions'=>['class'=>'form-control w10', 'step'=>1, 'min'=>0, 'max'=>5]]
                    ],
                    'comment'=>[
                        'type'=>'textArea',
                        'params'=>['htmlOptions'=>['class'=>'form-control w100', 'style'=>'min-height:300px']]
                    ],
                    'create_time'=>'dateTime'
               ]);
            }
        ]
    ]    
];
