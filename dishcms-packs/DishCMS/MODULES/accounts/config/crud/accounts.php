<?php
/**
 * Аккаунты
 * 
 */
use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use crud\components\helpers\HCrud;
use crud\models\ar\accounts\models\Account;

return [
    'class'=>'\crud\models\ar\accounts\models\Account',
    'access'=>[
        ['allow', 'users'=>['@'], 'roles'=>['admin']]
    ],
    'config'=>[
        'tablename'=>'accounts',
        'definitions'=>[
            'column.pk',
            'column.create_time',
            'column.update_time',
            'column.published'=>['label'=>'Активен'],
            'name'=>['type'=>'string', 'label'=>'ФИО'],
            'email'=>['type'=>'string', 'label'=>'E-Mail'],
            'phone'=>['type'=>'string', 'label'=>'Номер телефона'],
            'password'=>['type'=>'string', 'label'=>'Пароль'],
            'role'=>['type'=>'string', 'label'=>'Роль'],
            'comment'=>['type'=>'string', 'label'=>'Комментарий'],
            'login_time'=>['type'=>'DATETIME'],
            'confirm_code'=>['type'=>'string', 'label'=>'Код подтверждения'],
            'last_confirm_code'=>['type'=>'string', 'label'=>'Предыдущий код подтверждения'],
            'check_code'=>['type'=>'string', 'label'=>'Дополнительный код проверки'],
        ],
        'behaviors'=>[
            'accountBehavior'=>'\accounts\behaviors\AccountModelBehavior',
        ],
        'consts'=>[
            'ROLE_DEFAULT'=>'registered_user',
            'ROLE_REGISTERED'=>'registered_user',
        ],
    ],
    'events'=>[
        'onAfterInitWebUser'=>function($event) {
            Account::model()->accountBehavior->initWebUser($event->params['webUser']);
        },
        'onAfterAuthUserIdentity'=>function($event) {
            if(Account::model()->accountBehavior->authByUserIdentity($event->params['userIdentity'], true)) {
                \Yii::app()->request->redirect('/accounts/account/index');
            }
        }
    ],
    'settings'=>[
        // @todo необходимо добавить в общие настройки приложения /config/settings.php
        'accounts'=>[
            'class'=>'\accounts\models\AccountSettings',
            'breadcrumbs'=>['Пользователи'=>'/cp/crud/index?cid=accounts'],
            'title'=>'Настройки пользователей',
            'viewForm'=>'accounts.modules.admin.views.settings._account_settings'
        ]
    ],    
    'menu'=>[
        'backend'=>['label'=>'Пользователи']
    ],
    'buttons'=>[
        'create'=>['label'=>'Добавить пользователя'],
        'settings'=>['label'=>'Настройки']
    ],
    'crud'=>[
        'index'=>[            
            'url'=>'/cp/crud/index',
            'title'=>'Пользователи',
            'gridView'=>[
                'dataProvider'=>[
                    'criteria'=>[
                        'select'=>'`id`, `phone`, `name`, `email`, `role`, `published`, `create_time`, `login_time`, `comment`'
                    ],
		            'sort'=>['defaultOrder'=>'`name`, `create_time` DESC, `id` DESC']
                ],
                'summaryText'=>'',
                'columns'=>[
                    'column.id',                    
                    [
                        'type'=>'column.title',
                        'header'=>'Пользователь',
                        'attributeTitle'=>'name',
                        'info'=>[
                            'Дата регистрации'=>'\common\components\helpers\HYii::formatDate($data->create_time)',
                            'E-Mail'=>'$data->email',
                            'Телефон'=>'$data->formatPhone()',
                            'Комментарий'=>'$data->comment'
                        ]
                    ],
                    [
                        'name'=>'role',
                        'type'=>'raw',
                        'value'=>'$data->getRoleLabel()',
                        'headerHtmlOptions'=>['style'=>'width:10%;text-align:center;white-space:nowrap;'],
                        'htmlOptions'=>['style'=>'text-align:center;font-size:12px'],
                    ],
                    [
                        'name'=>'login_time',
                        'header'=>'Последний вход',
                        'headerHtmlOptions'=>['style'=>'width:10%;text-align:center;white-space:nowrap;'],
                        'htmlOptions'=>['style'=>'text-align:center;font-size:12px'],
                        'type'=>'raw',
                        'value'=>'$data->login_time ? \common\components\helpers\HYii::formatDate($data->login_time) : "<span class=\"label label-info\">Нет</span>"'
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
                                    'url'=>'\Yii::app()->createUrl("/cp/crud/update", ["cid"=>"accounts", "id"=>$data->id, "mode"=>"change_password"])',
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
                if($model->scenario == 'change_password') return "Изменение пароля пользователя &laquo;{$model->name}&raquo;";
                else return "Редактирование пользователя &laquo;{$model->name}&raquo;";
            },
            'onAfterSave'=>function($model) {
                if($model->scenario == 'change_password') {
                    Y::setFlash(Y::FLASH_SYSTEM_SUCCESS, "Пароль пользователя &laquo;{$model->name}&raquo; успешно изменен");
                    Y::controller()->redirect(HCrud::getConfigUrl('accounts', 'crud.index.url', '/crud/admin/default/index', ['cid'=>'accounts'], 'c'));
                }
            }
        ],
        'delete'=>[
            'url'=>['/cp/crud/delete'],
        ],
        'form'=>[
            // 'htmlOptions'=>['enctype'=>'multipart/form-data'],
            'attributes'=>function(&$model) {
                if(!$model->role) $model->role=Account::ROLE_DEFAULT;
                $model->phone=$model->formatPhone();
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
                        'name'
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
                    $attributes[]='email';
                    $attributes['phone']='phone';
                    $attributes['comment']=[
                        'type'=>'textArea',
                        'params'=>['htmlOptions'=>['class'=>'form-control w50']]
                    ];
                }
                return $attributes;
            }
        ]
    ]
];