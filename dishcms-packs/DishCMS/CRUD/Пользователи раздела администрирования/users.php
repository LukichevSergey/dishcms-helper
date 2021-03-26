<?php
/**
 * Пользователи
 * 
 * Для авторизации добавить в \UserAuth::extAuth() или \UserAuth::localAuth() перед финальным "return":
 * call_user_func_array([\crud\models\ar\Users::model(), 'authByUserIdentity'], [&$this]);
 * 
 * Для учитывания ролей добавить в \DWebUser::init()
 * call_user_func_array([\crud\models\ar\Users::model(), 'initWebUser'], [&$this]);
 */
use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use crud\components\helpers\HCrud;

return [
    'class'=>'\crud\models\ar\Users',
    'access'=>[
        ['allow', 'users'=>['@'], 'roles'=>['admin']]
    ],
    'config'=>[
        'tablename'=>'users',
        'definitions'=>[
            'column.pk',
            'column.create_time',
            'column.update_time',
            'column.published'=>['label'=>'Активен'],
            'name'=>['type'=>'string', 'label'=>'ФИО'],
            'email'=>['type'=>'string', 'label'=>'E-Mail'],
            'login'=>['type'=>'string', 'label'=>'Логин'],
            'password'=>['type'=>'string', 'label'=>'Пароль'],
            'role'=>['type'=>'string', 'label'=>'Роль'],
            'comment'=>['type'=>'string', 'label'=>'Комментарий'],
        ],
        'behaviors'=>[
            '.UserBehavior'
        ],
        'consts'=>[
            'ROLE_ADMIN'=>'admin'
        ],
        'methods'=>[
            'public function roles() {
                return [
                    self::ROLE_ADMIN=>"Администратор",
                    \crud\models\ar\extend\modules\comments\Comment::ROLE_MODERATOR=>"Модератор отзывов"
                ];
            }'
        ]
    ],
    'menu'=>[
        'backend'=>['label'=>'Пользователи']
    ],
    'buttons'=>[
        'create'=>['label'=>'Добавить пользователя'],
    ],
    'crud'=>[
        'index'=>[            
            'url'=>'/cp/crud/index',
            'title'=>'Пользователи',
            'gridView'=>[
                'dataProvider'=>[
                    'criteria'=>[
                        'select'=>'`t`.`id`, `login`, `name`, `email`, `role`, `published`, `create_time`, `comment`'
                    ],
		            'sort'=>['defaultOrder'=>'`login`, create_time DESC, id DESC']
                ],
                'summaryText'=>'',
                'columns'=>[
                    'column.id',                    
                    [
                        'type'=>'column.title',
                        'header'=>'Пользователь',
                        'attributeTitle'=>'login',
                        'info'=>[
                            'ФИО'=>'$data->name',
                            'E-Mail'=>'$data->email',
                            'Комментарий'=>'$data->comment'
                        ]
                    ],
                    [
                        'name'=>'role',
                        'type'=>'raw',
                        'value'=>'$data->getRoleLabel()',
                        'headerHtmlOptions'=>['style'=>'width:15%;text-align:center;white-space:nowrap;'],
                        'htmlOptions'=>['style'=>'text-align:center'],
                    ],
                    [
                        'name'=>'create_time',
                        'header'=>'Дата',
                        'headerHtmlOptions'=>['style'=>'width:10%;text-align:center;white-space:nowrap;'],
                        'htmlOptions'=>['style'=>'text-align:center'],
                    ],
                    [
                        'name'=>'published',
                        'header'=>'Активен',
                        'type'=>'common.ext.published',
                        'headerHtmlOptions'=>['style'=>'width:10%;text-align:center;white-space:nowrap;']
                    ],
                    'crud.buttons'=>[
                        'type'=>'crud.buttons',
                        'params'=>[
                            'template'=>'{change_password}{update}{delete}',
                            'buttons'=>[
                                'change_password'=>[
                                    'label'=>'<span class="glyphicon glyphicon-wrench"></span> Изменить пароль',
                                    'url'=>'\Yii::app()->createUrl("/cp/crud/update", ["cid"=>"users", "id"=>$data->id, "mode"=>"change_password"])',
                                    'options'=>['title'=>'Изменить пароль', 'class'=>'btn btn-xs btn-info w100', 'style'=>'margin-top:2px'],
                                ],
                                'update'=>[
                                    'label'=>'<span class="glyphicon glyphicon-user"></span> Профиль',
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
            'title'=>'Новый пользователь',
        ],
        'update'=>[
            'scenario'=>A::get($_REQUEST, 'mode', 'update'),
            'url'=>['/cp/crud/update'],
            'onBeforeSetTitle'=>function($model) {
                if($model->scenario == 'change_password') return "Изменение пароля пользователя &laquo;{$model->login}&raquo;";
                else return "Редактирование пользователя &laquo;{$model->login}&raquo;";
            },
            'onAfterSave'=>function($model) {
                if($model->scenario == 'change_password') {
                    Y::setFlash(Y::FLASH_SYSTEM_SUCCESS, "Пароль пользователя &laquo;{$model->login}&raquo; успешно изменен");
                    Y::controller()->redirect(HCrud::getConfigUrl('users', 'crud.index.url', '/crud/admin/default/index', ['cid'=>'users'], 'c'));
                }
            }
        ],
        'delete'=>[
            'url'=>['/cp/crud/delete'],
        ],
        'form'=>[
            'htmlOptions'=>['enctype'=>'multipart/form-data'],
            'attributes'=>function(&$model) {
                $attributes=[];
                if($model->scenario == 'change_password') {
                    $model->password='';
                    $model->repassword=''; 
                    $attributes=[
                        'password'=>'password',
                        'repassword'=>'password'
                    ];
                }
                else {
                    $attributes=[
                        'published'=>'checkbox',
                        'login'
                    ];
                    if($model->isNewRecord) {
                        $attributes['password']='password';
                        $attributes['repassword']='password';
                    }
                    $attributes['role']=[
                        'type'=>'dropDownList',
                        'params'=>[
                            'data'=>$model->roles(),
                            'htmlOptions'=>['class'=>'form-control w50', 'empty'=>'-- Роль пользователя --']
                        ]
                    ];
                    $attributes[]='name';
                    $attributes[]='email';
                    $attributes['comment']=[
                        'type'=>'textArea',
                        'params'=>['htmlOptions'=>['class'=>'form-control w50']]
                    ];
                }
                return $attributes;
            },
            'buttons'=>['delete'=>false]
        ]
    ]
];