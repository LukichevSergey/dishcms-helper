<?php
/**
 * Аккаунты
 * 
 */
use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HModel;
use crud\components\helpers\HCrud;
use crud\models\ar\accounts\models\Account;
use accounts\components\helpers\HAccount;

/** @var callable $t функция перевода **/

$t=Y::module('accounts')->t();

$account=null;
if(class_exists('\crud\models\ar\accounts\models\Account')) {
    $account=HModel::massiveAssignment('\crud\models\ar\accounts\models\Account', true, false, 'crud_filter');
}

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
            'column.published'=>['label'=>$t('crud.accounts.published')],
            'name'=>['type'=>'string', 'label'=>$t('crud.accounts.name')],
            'lastname'=>['type'=>'string', 'label'=>$t('crud.accounts.lastname')],
            'email'=>['type'=>'string', 'label'=>$t('crud.accounts.email')],
            'phone'=>['type'=>'string', 'label'=>$t('crud.accounts.phone')],
            'password'=>['type'=>'string', 'label'=>$t('crud.accounts.password')],
            'plain_password'=>['type'=>'string', 'label'=>$t('crud.accounts.plain_password')],
            'role'=>['type'=>'string', 'label'=>$t('crud.accounts.role')],
            'comment'=>['type'=>'string', 'label'=>$t('crud.accounts.comment')],
            'login_time'=>['type'=>'DATETIME', 'label'=>$t('crud.accounts.login_time')],
            'confirm_code'=>['type'=>'string', 'label'=>$t('crud.accounts.confirm_code')],
            'last_confirm_code'=>['type'=>'string', 'label'=>$t('crud.accounts.last_confirm_code')],
            'check_code'=>['type'=>'string', 'label'=>$t('crud.accounts.check_code')],
            'moderated'=>['type'=>'TINYINT(1) NOT NULL DEFAULT 0', 'label'=>$t('crud.accounts.moderated')],
        ],
        'behaviors'=>[
            'accountBehavior'=>'\accounts\behaviors\AccountModelBehavior',
        ],
        'consts'=>[
            'ROLE_RETAIL_BUYER'=>'retail_buyer',
            'ROLE_WHOLESALE_BUYER'=>'wholesale_buyer',
        ],
        'methods'=>[
			'public $privacy;',
			'public $remember_me;',
			'public $is_wholesale;',
        ]
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
    'menu'=>[
        'backend'=>['label'=>$t('crud.accounts.menu.backend')]
    ],
    'buttons'=>[
        'create'=>['label'=>$t('crud.accounts.buttons.create')],
        'settings'=>['id'=>'accounts', 'label'=>$t('crud.accounts.buttons.settings')],
        'custom'=>function() {
            $jsCode=<<<'EOL'
$(document).on('click','.js-btn-moderate',function(e){if(confirm('Подтвердите активацию аккаунта.')){
let btn=$(e.target);$.post('/accounts/admin/default/confirmRegistration',{id:btn.data('id')},function(r){
if(r.success){if(!r.data.sended){alert('Не удалось отправить письмо об активации аккаунта');}
btn.siblings('.js-lastlogin').text('Нет');btn.parents('tr').find('.js-active-mark').removeClass('marked').addClass('unmarked');
btn.remove();}else{alert('Не удалось активировать аккаунт');}},'json');}e.preventDefault();return false;});
EOL;
            Y::js(false, $jsCode, \CClientScript::POS_READY);
            
            Y::js('accounts-check-counts', '(function(){function update(){
let ids=[];$(".grid-view table tr").each(function(){ids.push($(this).attr("id"));});
    $.post("/accounts/admin/default/getAccountCounts",{ids:ids},function(r){
        if(r.success){let tr;
            for(let id in r.data.acc) {tr=$(".grid-view table tr[id="+id+"]");
                if(tr.length){
                    tr.find(".js-account-orders-count").text(r.data.acc[id].ordersCount);
                }
            }
        }
    },"json");
}setInterval(update,10000);update();})();', \CClientScript::POS_READY);
        }
    ],
    'crud'=>[
        'index'=>[            
            'url'=>'/cp/crud/index',
            'title'=>'Пользователи',
            'gridView'=>[
                'filter'=>function() {
                    $model=HModel::massiveAssignment('\crud\models\ar\accounts\models\Account', true, false, 'crud_filter');
                    $model->id=A::rget($_REQUEST, 'crud_models_ar_accounts_models_Account.id');
                    Y::css('account_filter', '.grid-view .filters td:first-child{padding:8px 1px;font-size:9px;}.grid-view .filters td:first-child input{height:22px;text-align:center;}');
                    return $model;
                },
                'dataProvider'=>call_user_func(function(){
                    $criteria=new \CDbCriteria();
                    
                    $attributes=A::get($_REQUEST, 'crud_models_ar_accounts_models_Account', []);
                    if(!empty($attributes['id'])) {
                        $criteria->addColumnCondition(['id'=>(int)$attributes['id']]);
                    }
                    
                    if(!empty($attributes['phone'])) {
                        $regexp=preg_replace('/[^0-9.*()\^$]+/', '', '^.*' . preg_replace('/(\d+)/', '($1).*', $attributes['phone']) . '$');
                        $criteria->addCondition(new \CDbExpression("CONCAT('+', `phone_country_code`, ' ', `phone`) REGEXP '{$regexp}'"));
                    }
                    
                    if(is_numeric($attributes['published'])) {
                        if((int)$attributes['published'] == -1) {
                            $criteria->addColumnCondition(['moderated'=>0]);
                        }
                        elseif((int)$attributes['published'] == -2) {
                            $criteria->addColumnCondition(['published'=>0]);
                        }
                        else {
                            $criteria->addColumnCondition(['published'=>1]);
                        }
                    }
                    if(!empty($attributes['name'])) {
                        $searchCriteria=new \CDbCriteria();
                        $searchCriteria->addSearchCondition('name', $attributes['name'], true, 'OR');
						$searchCriteria->addSearchCondition('lastname', $attributes['name'], true, 'OR');
                        $searchCriteria->addSearchCondition('email', $attributes['name'], true, 'OR');
                        $searchCriteria->addSearchCondition('comment', $attributes['name'], true, 'OR');
                        $criteria->mergeWith($searchCriteria);
                    }

					if(class_exists('\crud\models\ar\accounts\models\Account')) {
						if(!empty($attributes['is_wholesale'])) {
							if((int)$attributes['is_wholesale'] === 2) { $criteria->addColumnCondition(['role'=>Account::ROLE_WHOLESALE_BUYER]); }
							elseif((int)$attributes['is_wholesale'] === 1) { $criteria->addColumnCondition(['role'=>Account::ROLE_RETAIL_BUYER]); }
						}
					}
                    
                    return [
                        'criteria'=>$criteria,
                        'sort'=>['defaultOrder'=>'`create_time` DESC, `id` DESC'],
                    ];
                }),
                'summaryText'=>'',
                'columns'=>[
                    [
                        'name'=>'id',
                        'header'=>'#',
                        'headerHtmlOptions'=>['style'=>'width:1%;text-align:center;white-space:nowrap;font-size:12px;'],
                        'htmlOptions'=>['style'=>'padding:8px 0;text-align:center;font-size:12px'],
                    ],
                    [
                        'type'=>'column.title',
                        'header'=>'Пользователь',
                        'attributeTitle'=>'name',
						'attributePrintTitle'=>'fullName',
                        'info'=>[
                            'Последний вход'=>'$data->login_time ? \common\components\helpers\HYii::formatDate($data->login_time) 
                                : ($data->moderated ? "Нет" : \CHtml::tag("span", ["class"=>"js-lastlogin"], "Ожидает модерацию<br/>") . \CHtml::link("Подтвердить регистрацию", "javascript:;", ["class"=>"btn btn-xs btn-danger js-btn-moderate", "data-id"=>$data->id]))',
                            'E-Mail'=>'$data->email',
                            'Контактный телефон'=>'$data->formatPhone()',
                            'Комментарий'=>'$data->comment',
                        ],
                        'headerHtmlOptions'=>['style'=>'width:50%;text-align:center;white-space:nowrap;font-size:12px;'],
                        'htmlOptions'=>['style'=>'font-size:12px'],
                    ],
                    [
                        'name'=>'is_wholesale',
						'filter'=>['2'=>'Оптовый', '1'=>'Розничный'],
                        'header'=>'Тип',
                        'type'=>'raw',
                        'headerHtmlOptions'=>['style'=>'width:15%;text-align:center;white-space:nowrap;font-size:12px;'],
                        'htmlOptions'=>['style'=>'padding:8px 0;text-align:center;font-size:12px'],
                        'value'=>function($data) {
                            if($data->role == Account::ROLE_WHOLESALE_BUYER) {
                                return \CHtml::tag('span', ['class'=>'label label-primary'], 'Оптовый покупатель');
                            }
                            elseif($data->role == Account::ROLE_RETAIL_BUYER) {
                                return \CHtml::tag('span', ['class'=>'label label-info'], 'Розничный покупатель');
                            }
                            else {
                                return \CHtml::tag('span', ['class'=>'label label-default'], 'Не определен');
                            }
                        },
                    ],                    
                    [
                        'name'=>'create_time',
                        'header'=>'Регистрация',
                        'filter'=>false,
                        'headerHtmlOptions'=>['style'=>'width:10%;text-align:center;white-space:nowrap;font-size:12px;'],
                        'htmlOptions'=>['style'=>'text-align:center;font-size:12px'],
                        'type'=>'raw',
                        'value'=>'\common\components\helpers\HYii::formatDate($data->create_time)'
                    ],
                    [
                        'name'=>'published',
                        'header'=>'Активен',
                        'filter'=>['1'=>'Да', '-2'=>'Нет', '-1'=>'Ожидает модерацию'],
                        'type'=>'common.ext.published',
                        'headerHtmlOptions'=>['style'=>'width:5%;text-align:center;white-space:nowrap;font-size:12px;']
                    ],
                    'crud.buttons'=>[
                        'type'=>'crud.buttons',
                        'params'=>[
                            'template'=>'{orders}{change_password}{update}{delete}',
                            'buttons'=>[
                                'orders'=>[
                                    'label'=>'<span class="glyphicon glyphicon-th-list"></span> Заказы (<span class="js-account-orders-count">...</span>)',
                                    'url'=>'\Yii::app()->createUrl("/cp/order", ["account_id"=>$data->id])',
                                    'options'=>['title'=>'Объявления', 'class'=>'btn btn-xs btn-success w100', 'style'=>'margin-top:2px'],
                                ],
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
            'scenario'=>'crud_create',
            'url'=>'/cp/crud/create',
            'title'=>'Новый пользователь',
        ],
        'update'=>[
            'scenario'=>A::get($_REQUEST, 'mode', 'crud_update'),
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
            'htmlOptions'=>['enctype'=>'multipart/form-data'],
            'attributes'=>function(&$model) {
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
                    if(!$model->isNewRecord) {
                        $model->is_wholesale=(int)HAccount::isWholesaleBuyer($model);
                    }
                    $attributes=[
                        'published'=>'checkbox',                        
                        'is_wholesale'=>'checkbox',                        
                        'name',
                        'lastname',                        
                    ];
                    
                    if($model->isNewRecord) {
                        $attributes['password']='password';
                        $attributes['repassword']='password';
                    }
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
