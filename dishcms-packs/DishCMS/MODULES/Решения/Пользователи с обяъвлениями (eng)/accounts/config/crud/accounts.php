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
use crud\models\ar\accounts\models\Country;

$account=null;
if(class_exists('\crud\models\ar\accounts\models\Account')) {
    $account=HModel::massiveAssignment('\crud\models\ar\accounts\models\Account', true, false, 'crud_filter');
}
$country=null;
if(class_exists('\crud\models\ar\accounts\models\Country')) {
    $country=HModel::massiveAssignment('\crud\models\ar\accounts\models\Country', true, false, 'crud_filter');
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
            'column.published'=>['label'=>'Активен'],
            'name'=>['type'=>'string', 'label'=>'Name'],
            'category'=>['type'=>'string', 'label'=>'Category'],
            'country_id'=>['type'=>'integer', 'label'=>'Country'],
            'company'=>['type'=>'string', 'label'=>'Company'],
            'email'=>['type'=>'string', 'label'=>'E-Mail'],
            'phone'=>['type'=>'string', 'label'=>'Phone'],
            'phone_mask'=>['type'=>'string', 'label'=>'Phone mask'],
            'phone_country'=>['type'=>'string', 'label'=>'Phone country code'],
            'phone_country_code'=>['type'=>'string', 'label'=>'Phone country code iso2'],
            'password'=>['type'=>'string', 'label'=>'Password'],
            'role'=>['type'=>'string', 'label'=>'Тип пользователя'],
            'comment'=>['type'=>'string', 'label'=>'Комментарий'],
            'login_time'=>['type'=>'DATETIME'],
            'confirm_code'=>['type'=>'string', 'label'=>'Код подтверждения'],
            'last_confirm_code'=>['type'=>'string', 'label'=>'Предыдущий код подтверждения'],
            'check_code'=>['type'=>'string', 'label'=>'Дополнительный код проверки'],
            'moderated'=>['type'=>'TINYINT(1) NOT NULL DEFAULT 0', 'label'=>'Модерация пройдена'],
            'column.image'=>['name'=>'company_logo', 'label'=>'Логотип компании', 'behaviorName'=>'companyLogoBehavior', 'name_alt'=>false]
        ],
        'behaviors'=>[
            'accountBehavior'=>'\accounts\behaviors\AccountModelBehavior',
            'bankInfoBehavior'=>[
                'class'=>'\common\ext\dataAttribute\behaviors\DataAttributeBehavior',
                'attribute'=>'bank_info',
                'attributeLabel'=>'Информация о банке'
            ]
        ],
        'consts'=>[
            'ROLE_AIRLINE_MRO'=>'airline_mro',
            'ROLE_AIRPORT'=>'airport',
            'ROLE_RESELLER'=>'reseller',
            'CATEGORY_AIRLINE_MRO'=>'airline_mro',
            'CATEGORY_AIRPORT'=>'airport',
            'CATEGORY_RESELLER'=>'reseller',
        ],
        'methods'=>[
            function() {
                ob_start();?>
                public $captcha;
                public $coupon_code='';
                public $coupon_check_code='';
                public $plain_password='';
			    public $privacy;
			    public $remember_me;
                public function validateName($attribute)
                {
                    if($this->$attribute) {
                        if(
                            ($a=(($a1=trim($this->$attribute, '- ')) != ($a2=trim($this->$attribute))))
                            || !preg_match('/^[абвгдеёжзийклмнопрстуфхцчшщъыьэюяАБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯa-zA-Z\-\s]+$/', trim($this->$attribute))
                            || $c=preg_match('/^[\-]+$/', trim($this->$attribute))
                            )
                        {
                            $this->addError($attribute, 'Only letters, spaces and hyphens are allowed.');
                        }
                    }
                }
                public function validateStrongPassword($attribute)
                {
                    if($this->$attribute) {
                        if(!preg_match('/((?=.*[a-z])(?=.*[A-Z])(?=.*[\\\\@#$%^&*\[\]\/!:;.,?\-+]).{6,20})/', $this->$attribute)) {
                            $this->addError($attribute, 'The password must contain at least one uppercase letter and at least one special character, for example: @#$%^&*-+[]\/!:;.,?');
                        }
                    }
                }
                public function afterValidate() {
                	return !$this->hasErrors();
                } 
                <?php 
                return ob_get_clean();
            }
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
        'settings'=>['label'=>'Настройки'],
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
                    tr.find(".js-account-adverts-count").text(r.data.acc[id].advertsCount);
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
                    if(!empty($attributes['company'])) {
                        $criteria->addSearchCondition('company', $attributes['company']);
                    }
                    if(!empty($attributes['phone'])) {
                        $regexp=preg_replace('/[^0-9.*()\^$]+/', '', '^.*' . preg_replace('/(\d+)/', '($1).*', $attributes['phone']) . '$');
                        $criteria->addCondition(new \CDbExpression("CONCAT('+', `phone_country_code`, ' ', `phone`) REGEXP '{$regexp}'"));
                    }
                    if(!empty($attributes['category'])) {
                        $criteria->addColumnCondition(['category'=>$attributes['category']]);
                    }
                    if(!empty($attributes['country_id'])) {
                        $criteria->addColumnCondition(['country_id'=>$attributes['country_id']]);
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
                        $searchCriteria->addSearchCondition('email', $attributes['name'], true, 'OR');
                        $searchCriteria->addSearchCondition('comment', $attributes['name'], true, 'OR');
                        $criteria->mergeWith($searchCriteria);
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
                        'info'=>[
                            'Последний вход'=>'$data->login_time ? \common\components\helpers\HYii::formatDate($data->login_time) 
                                : ($data->moderated ? "Нет" : \CHtml::tag("span", ["class"=>"js-lastlogin"], "Ожидает модерацию<br/>") . \CHtml::link("Подтвердить регистрацию", "javascript:;", ["class"=>"btn btn-xs btn-danger js-btn-moderate", "data-id"=>$data->id]))',
                            'E-Mail'=>'$data->email',
                            'Комментарий'=>'$data->comment',
                        ],
                        'headerHtmlOptions'=>['style'=>'width:25%;text-align:center;white-space:nowrap;font-size:12px;'],
                        'htmlOptions'=>['style'=>'font-size:12px'],
                    ],
                    [
                        'name'=>'phone',
                        'header'=>'Телефон',
                        'type'=>'raw',
                        'headerHtmlOptions'=>['style'=>'width:15%;text-align:center;white-space:nowrap;font-size:12px;'],
                        'htmlOptions'=>['style'=>'padding:8px 0;text-align:center;font-size:12px'],
                        'value'=>'$data->formatPhone()',
                    ],
                    [
                        'name'=>'company',
                        'header'=>'Компания',
                        'type'=>'raw',
                        'headerHtmlOptions'=>['style'=>'width:12%;text-align:center;white-space:nowrap;font-size:12px;'],
                        'htmlOptions'=>['style'=>'text-align:center;font-size:12px'],
                        'value'=>'$data->company',
                    ],
                    [
                        'name'=>'category',
                        'header'=>'Категория',
                        'filter'=>$account ? $account->categoryLabels() : false,
                        'type'=>'raw',
                        'headerHtmlOptions'=>['style'=>'width:10%;text-align:center;white-space:nowrap;font-size:12px;'],
                        'htmlOptions'=>['style'=>'text-align:center;font-size:12px'],
                        'value'=>'$data->getCategoryLabel()',
                    ],
                    [
                        'name'=>'country_id',
                        'header'=>'Страна',
                        'filter'=>$country ? $country->bySort()->listData('title', ['order'=>'title']) : false,
                        'headerHtmlOptions'=>['style'=>'width:10%;text-align:center;white-space:nowrap;font-size:12px;'],
                        'htmlOptions'=>['style'=>'text-align:center;font-size:12px'],
                        'type'=>'raw',
                        'value'=>'$data->country ? $data->country->title : ""'
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
                            'template'=>'{advertisements}{change_password}{update}{delete}',
                            'buttons'=>[
                                'advertisements'=>[
                                    'label'=>'<span class="glyphicon glyphicon-th-list"></span> Объявления (<span class="js-account-adverts-count">...</span>)',
                                    'url'=>'\Yii::app()->createUrl("/cp/crud/index", ["cid"=>"accounts_adverts", "crud_models_ar_accounts_models_Advert[account_id]"=>$data->id])',
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
                    if(!$model->phone_country){
                        $model->phone_country='us';
                    }
                    
                    $attributes=[
                        'published'=>'checkbox',
                        'category'=>[
                            'type'=>'dropDownList',
                            'params'=>[
                                'data'=>$model->categoryLabels(),
                                'htmlOptions'=>['class'=>'form-control w50', 'empty'=>'-- Категория --']
                            ]
                        ],
                        'name',
                        'company',
                        'country_id'=>[
                            'type'=>'dropDownList',
                            'params'=>[
                                'data'=>Country::model()->published()->bySort()->listData('title', ['order'=>'region_id, title', 'select'=>'id, title, region_id'], null, 'id', function($country) {
                                    return $country->region->title;
                                }),
                                'htmlOptions'=>['class'=>'form-control w50', 'empty'=>'-- выберите страну --']
                            ]
                        ]
                    ];
                    
                    if($model->isNewRecord) {
                        $attributes['password']='password';
                        $attributes['repassword']='password';
                    }
                    $attributes[]='email';
                    $attributes['phone']=[
                        'type'=>'i18nPhone',
                        'params'=>[
                            'attributeMask'=>'phone_mask',
                            'attributeCountry'=>'phone_country',
                            'attributeCountryCode'=>'phone_country_code',
                            'options'=>[
                                'preferredCountries'=>array_keys($model->getCountryPreferrerLabels()),
                                'onlyCountries'=>array_keys($model->getCountryLabels())                            
                        ]]
                    ];
                    $attributes['comment']=[
                        'type'=>'textArea',
                        'params'=>['htmlOptions'=>['class'=>'form-control w50']]
                    ];                    
                    $attributes['company_logo']=[
                        'type'=>'common.ext.file.image',
                        'behaviorName'=>'companyLogoBehavior'
                    ];
                }
                return $attributes;
            }
        ]
    ]
];