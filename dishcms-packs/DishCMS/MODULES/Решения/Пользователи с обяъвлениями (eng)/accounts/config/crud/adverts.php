<?php
use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HRequest as R;
use common\components\helpers\HModel;
use crud\models\ar\accounts\models\Account;
use crud\models\ar\accounts\models\Advert;
use crud\models\ar\accounts\models\Region;
use crud\models\ar\accounts\models\Country;

return [
    'class'=>'\crud\models\ar\accounts\models\Advert',
    'relations'=>[
        'accounts_advert_emails'=>[
            'type'=>'has_many',
            'attribute'=>'advert_id'
        ],        
    ],
    'config'=>[
        'tablename'=>'accounts_adverts',
        'definitions'=>[
            'column.pk',
            'column.published'=>['behaviorParams'=>['onUpdateChangeActive'=>['\crud\models\ar\accounts\models\Advert', 'onUpdateChangeActive']]],
            'column.create_time',
            'column.update_time',
            'foreign.account_id'=>['label'=>'Пользователь'],
            'type'=>['type'=>'INT(11) NOT NULL DEFAULT 0', 'label'=>'Тип объявления'],
            'part_number'=>['type'=>'VARCHAR(255) NOT NULL DEFAULT \'\'', 'label'=>'Part Number'],
            'part_type'=>['type'=>'VARCHAR(255) NOT NULL DEFAULT \'\'', 'label'=>'Type of part'],
            'quantity'=>['type'=>'INT(11) NOT NULL DEFAULT 0', 'label'=>'Quantity'],
            'code'=>['type'=>'VARCHAR(255) NOT NULL DEFAULT \'\'', 'label'=>'Condition / Capability Code'],
            'category'=>['type'=>'VARCHAR(255) NOT NULL DEFAULT \'\'', 'label'=>'Category'],
            'more_info'=>['type'=>'LONGTEXT', 'label'=>'More Information'],
            'published_date'=>['type'=>'DATETIME', 'label'=>'Published Date'],
            'column.file'=>[
                'label'=>'Document',
                'types'=>'xls, xlsx',
                'mimeTypes'=>[
                    'application/vnd.ms-excel',
                    'application/msexcel',
                    'application/x-msexcel',
                    'application/x-ms-excel',
                    'application/x-excel',
                    'application/x-dos_ms_excel',
                    'application/xls',
                    'application/x-xls',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                ]
            ],
            'detail_type'=>['type'=>'string', 'label'=>'Object'],
            'detail_type_value'=>['type'=>'string', 'label'=>'Object Type Value']
        ],
        'behaviors'=>[
            'advertBehavior'=>'\accounts\behaviors\AdvertModelBehavior',
        ],
        'consts'=>[
            'TYPE_SALE'=>1,
            'TYPE_PARTS_WANTED'=>2,  
            'DETAIL_TYPE_AIRCRAFT'=>'aircraft',
            'DETAIL_TYPE_EQUIPMENT'=>'equipment',
            'TYPE_AIRLINE_PARTS_WANTED'=>1,
            'TYPE_AIRLINE_FOR_SALE'=>2,
            'TYPE_AIRPORT_EQUIPMENT_WANTED'=>3,
            'TYPE_AIRPORT_FOR_SALE'=>4
        ],
        'methods'=>[
            'public $account_region_id;',
            'public $account_category;',
            'public $advert_filter_info;',
            'public $last_published=null;',
            'public $last_file=null;',
            'public $ignoreSendPublishedEmail=false;',
            'public static function onUpdateChangeActive($model, $newValue) {
                if((int)$newValue === 1) {
                    $model->published_date=new \CDbExpression("NOW()");
                    $model->update(["published_date"]);
                    \common\components\helpers\HEvent::raise("onAccountAdvertPublished", ["advert"=>$model, "account"=>$model->account]);
                }
                else {
                    \common\components\helpers\HEvent::raise("onAccountAdvertUnpublished", ["advert"=>$model, "account"=>$model->account]);
                }
            }'
        ]
    ],
    'buttons'=>[
        'create'=>['label'=>''],
        'custom'=>function() {
        Y::js('adverts-check-status', 'setInterval(function(){$(".js-advert-status").each(function(){
if($(this).parents("tr:first").find(".js-advert-published").find(".marked").length) {$(this).removeClass("label-success").addClass("label-warning").text("Awaiting moderation");}
else {$(this).removeClass("label-warning").addClass("label-success").text("Published");}});}, 500);', \CClientScript::POS_READY);
        
        Y::js('adverts-check-counts', '(function(){function update(){
let ids=[];$(".grid-view table tr").each(function(){ids.push($(this).attr("id"));});
    $.post("/accounts/admin/default/getAdvertCounts",{ids:ids},function(r){
        if(r.success){let tr;
            for(let id in r.data.ads) {tr=$(".grid-view table tr[id="+id+"]");
                if(tr.length){
                    tr.find(".js-advert-responses-count").text(r.data.ads[id].respondsCount);
                    tr.find(".js-advert-emails-count").text(r.data.ads[id].emailsCount);
                }
            }
        }
    },"json");
}setInterval(update,10000);update();})();', \CClientScript::POS_READY);        
        }
    ],
    'crud'=>[
        'breadcrumbs'=>[
            'Пользователи'=>'/cp/crud/index?cid=accounts'
        ],
        'index'=>[
            'url'=>'/cp/crud/index',
            'title'=>'Объявления',
            'gridView'=>[
                'filter'=>function() {
                    $model=HModel::massiveAssignment('\crud\models\ar\accounts\models\Advert', true, false, 'crud_filter');
                    $model->id=A::rget($_REQUEST, 'crud_models_ar_accounts_models_Advert.id');
                    Y::css('account_advert_filter', '.grid-view .filters td:first-child{padding:8px 1px;font-size:9px;}.grid-view .filters td:first-child input{height:22px;text-align:center;}');
                    return $model;
                },
                'dataProvider'=>call_user_func(function(){
                    $criteria=new \CDbCriteria();
                    
                    $attributes=A::get($_REQUEST, 'crud_models_ar_accounts_models_Advert', []);
                    if(!empty($attributes['id'])) {
                        $criteria->addSearchCondition('id', $attributes['id']);
                    }
                    
                    if(!empty($attributes['type'])) {
                        $criteria->addColumnCondition(['type'=>(int)$attributes['type']]);
                    }
                    
                    if(!empty($attributes['account_id'])) {
                        if(is_numeric($attributes['account_id'])) {
                            $criteria->addCondition('account_id='.(int)$attributes['account_id']);
                        }
                        elseif(class_exists('\crud\models\ar\accounts\models\Account')) {
                            $accountSearchCriteria=new \CDbCriteria();
                            $accountSearchCriteria->index='id';
                            $accountSearchCriteria->select='id';
                            $accountSearchCriteria->addSearchCondition('name', $attributes['account_id']);
                            if($foundAccounts=Account::model()->resetScope()->findAll($accountSearchCriteria)) {
                                $criteria->addInCondition('account_id', array_keys($foundAccounts));
                            }
                            else {
                                $criteria->addCondition('1<>1');
                            }
                        }
                    }
                    
                    if(!empty($attributes['account_region_id']) && class_exists('\crud\models\ar\accounts\models\Account') && class_exists('\crud\models\ar\accounts\models\Country')) {
                        $isRegionIdEmpty=true;
                        if($countries=Country::model()->resetScope()->findAllByAttributes(['region_id'=>$attributes['account_region_id']], ['index'=>'id', 'select'=>'id'])) {
                            $countriesCriteria=new \CDbCriteria();
                            $countriesCriteria->addInCondition('country_id', array_keys($countries));
                            $countriesCriteria->index='id';
                            $countriesCriteria->select='id';
                            if($foundAccounts=Account::model()->resetScope()->findAll($countriesCriteria)) {
                                $criteria->addInCondition('account_id', array_keys($foundAccounts));
                                $isRegionIdEmpty=false;
                            }
                        }
                        if($isRegionIdEmpty) {
                            $criteria->addCondition('1<>1');
                        }
                    }
                    
                    if(!empty($attributes['account_category']) && class_exists('\crud\models\ar\accounts\models\Account')) {
                        if($foundAccounts=Account::model()->resetScope()->findAllByAttributes(['category'=>$attributes['account_category']], ['index'=>'id', 'select'=>'id'])) {
                            $criteria->addInCondition('account_id', array_keys($foundAccounts));
                        }
                        else {
                            $criteria->addCondition('1<>1');
                        }
                    }
                    
                    if(is_numeric($attributes['published'])) {
                        if((int)$attributes['published'] == -2) {
                            $criteria->addColumnCondition(['published'=>0]);
                        }
                        else {
                            $criteria->addColumnCondition(['published'=>1]);
                        }
                    }
                    
                    if(!empty($attributes['advert_filter_info'])) {
                        $searchCriteria=new \CDbCriteria();
                        $searchCriteria->addSearchCondition('part_number', $attributes['advert_filter_info'], true, 'OR');
                        $searchCriteria->addSearchCondition('part_type', $attributes['advert_filter_info'], true, 'OR');
                        $searchCriteria->addSearchCondition('code', $attributes['advert_filter_info'], true, 'OR');
                        $searchCriteria->addSearchCondition('aircraft', $attributes['advert_filter_info'], true, 'OR');
                        $searchCriteria->addSearchCondition('category', $attributes['advert_filter_info'], true, 'OR');
                        $searchCriteria->addSearchCondition('more_info', $attributes['advert_filter_info'], true, 'OR');
                        $criteria->mergeWith($searchCriteria);
                    }
                    
                    return [
                        'criteria'=>$criteria,
                        'sort'=>['defaultOrder'=>'`published`, IF(ISNULL(`update_time`), `create_time`, `update_time`) DESC, `id` DESC'],
                    ];
                }),
                'summaryText'=>'Объявления {start} - {end} из {count}',
                'columns'=>[
                    [
                        'type'=>'column.id',
                        'headerHtmlOptions'=>['style'=>'text-align:center;'],
                    ],
                    [
                        'type'=>'column.title',
                        'name'=>'advert_filter_info',
                        'attributeTitle'=>'advertTitle',
                        'disableLink'=>true,
                        'header'=>'Объявление',
                        'headerHtmlOptions'=>['style'=>'text-align:center;'],
                        'info'=>[
                            'Status'=>'"<span class=\"label label-default js-advert-status\"></span>"',
                            'Part Number'=>'$data->part_number',
                            'Type of part'=>'$data->part_type',
                            'Quantity'=>'$data->quantity',
                            'Condition / Capability Code'=>'$data->code',
                            ':expr:$data->getDetailTypeLabel()'=>'$data->detail_type_value',
                            'Category'=>'$data->category',
                            'Document'=>'($data->fileBehavior->exists() ? $data->fileBehavior->downloadLink() : "")',
                            'More Information'=>'$data->more_info',
                        ]
                    ],
                    [
                        'name'=>'type',
                        'header'=>'Тип',
                        'type'=>'raw',
                        'filter'=>class_exists('\crud\models\ar\accounts\models\Advert') ? [Advert::TYPE_SALE=>'Продажа', Advert::TYPE_PARTS_WANTED=>'Покупка'] : [],
                        'headerHtmlOptions'=>['style'=>'width:10%;text-align:center;white-space:nowrap;'],
                        'htmlOptions'=>['style'=>'text-align:center;'],
                        'value'=>function($data) {
                            return ($data->type == Advert::TYPE_SALE) ? 'Продажа' : 'Покупка';
                        }
                    ],
                    [
                        'name'=>'account_id',
                        'header'=>'Пользователь',
                        'type'=>'raw',
                        'headerHtmlOptions'=>['style'=>'width:10%;text-align:center;white-space:nowrap;'],
                        'htmlOptions'=>['style'=>'text-align:center;'],
                        'value'=>function($data) {
                            if($account=$data->getRelated('account')) {
                                return \CHtml::link($account->name, '/cp/crud/index?cid=accounts&crud_models_ar_accounts_models_Account[id]=' . $account->id);
                            }
                        }
                    ],
                    [
                        'name'=>'account_region_id',
                        'header'=>'Регион',
                        'filter'=>class_exists('\crud\models\ar\accounts\models\Region') ? Region::model()->listData('title', ['order'=>'sort']) : false,
                        'headerHtmlOptions'=>['style'=>'width:10%;text-align:center;white-space:nowrap;'],
                        'htmlOptions'=>['style'=>'text-align:center;'],
                        'type'=>'raw',
                        'value'=>function($data) {
                            if($account=$data->getRelated('account')) {
                                if($country=$account->getRelated('country')) {
                                    if($region=$country->getRelated('region')) {
                                        return $region->title;
                                    }
                                }
                            }
                        }
                    ],
                    [
                        'name'=>'account_category',
                        'filter'=>class_exists('\crud\models\ar\accounts\models\Account') ? Account::model()->categoryLabels() : false,
                        'header'=>'Категория',
                        'headerHtmlOptions'=>['style'=>'width:10%;text-align:center;white-space:nowrap;'],
                        'htmlOptions'=>['style'=>'text-align:center;'],
                        'type'=>'raw',
                        'value'=>function($data) {
                            if($account=$data->getRelated('account')) {
                                return $account->getCategoryLabel();
                            }
                        }
                    ],
                    [
                        'name'=>'create_time',
                        'filter'=>false,
                        'header'=>'Дата',
                        'headerHtmlOptions'=>['style'=>'width:10%;text-align:center;white-space:nowrap;'],
                        'htmlOptions'=>['style'=>'text-align:center;font-size:12px'],
                        'type'=>'raw',
                        'value'=>function($data) {
                            $value=date_create_from_format("Y-m-d H:i:s", $data->create_time)->format("l d/m/Y g:i A");
                            if(!\common\components\helpers\HTools::isDateEmpty($data->update_time)) {
                                if($data->update_time != $data->create_time) {
                                    $value.='<br/><br/><small><b>updated:</b></small><br/>' . date_create_from_format("Y-m-d H:i:s", $data->update_time)->format("l d/m/Y g:i A");
                                }
                            }
                            return $value;
                        }
                    ],
                    [
                        'name'=>'published',
                        'filter'=>['1'=>'Опубликован', '-2'=>'Ожидает модерацию'],
                        'header'=>'Опубл.',
                        'type'=>'common.ext.published',
                        'headerHtmlOptions'=>['style'=>'width:10%;text-align:center;white-space:nowrap;'],
                        'htmlOptions'=>['class'=>'js-advert-published']
                    ],
                    'crud.buttons'=>[
                        'type'=>'crud.buttons',
                        'params'=>[
                            'template'=>'{responses}{emails}{update}{delete}',
                            'buttons'=>[
                                'emails'=>[
                                    'label'=>'<span class="glyphicon glyphicon-th-list"></span> Уведомления (<span class="js-advert-emails-count">...</span>)',
                                    'url'=>'\Yii::app()->createUrl("/cp/crud/index", ["cid"=>"accounts_advert_emails", "accounts_adverts"=>$data->id])',
                                    'options'=>['title'=>'История почтовых уведомлений', 'class'=>'btn btn-xs btn-info w100', 'style'=>'margin-top:2px'],                                    
                                ],
                                'responses'=>[
                                    'label'=>'<span class="glyphicon glyphicon-envelope"></span> Отклики (<span class="js-advert-responses-count">...</span>)',
                                    'url'=>'\Yii::app()->createUrl("/cp/crud/index", ["cid"=>"accounts_advert_responses", "accounts_adverts"=>$data->id])',
                                    'options'=>['title'=>'Отклики', 'class'=>'btn btn-xs btn-success w100', 'style'=>'margin-top:2px'],
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
            'onBeforeLoad'=>function(){R::e404();},
            'url'=>'/cp/crud/create',
            'title'=>'Новое объявление',
        ],
        'update'=>[
            'scenario'=>'crud_update',
            'onBeforeSetTitle'=>function($model) {
                return 'Редактирование объявления №' . $model->id;
            },
            'onAfterSave'=>function($model) {
                // если опубликовано, то отправлять уведомление
            },
            'onBeforeModelLoad'=>function(&$model) {
                $model->last_published=$model->published;
            },
            
            'url'=>'/cp/crud/update',
            'title'=>'Редактировать объявление',
        ],
        'delete'=>[
            'url'=>'/cp/crud/delete',
        ],
        'form'=>[
            'htmlOptions'=>[
                'enctype'=>'multipart/form-data'
            ],
            'attributes'=>function($model) {
                $account=Account::modelById($model->account_id);
                Y::js(false, '$(document).on("change",".js-account-id",function(e){let f=$(e.target).parents("form:first");
if(f.find(".js-detail-type").length){f.find(".js-detail-type").attr("disabled", "disabled");}
if(f.find(".js-detail-type-value").length){f.find(".js-detail-type-value").attr("disabled", "disabled");}
f.find(".js-detail-type-alert").show();
});', \CClientScript::POS_READY);
                $attributes=[
                    'published'=>'checkbox',
                    'account_id'=>[
                        'type'=>'dropDownList',
                        'params'=>[
                            'data'=>Account::model()->listData('name'),
                            'htmlOptions'=>['class'=>'form-control w100 js-account-id']
                        ]
                    ],
                    'type'=>[
                        'type'=>'radioList',
                        'params'=>[
                            'data'=>[
                                Advert::TYPE_SALE=>'Продажа',
                                Advert::TYPE_PARTS_WANTED=>'Покупка',
                            ]
                        ]
                    ],
                    'part_number',
                    'part_type',
                    'quantity'=>[
                        'type'=>'number',
                        'params'=>['htmlOptions'=>['class'=>'form-control w10']]
                    ],
                    'code'
                ];
                
                if($account->category == Account::CATEGORY_AIRLINE_MRO) {
                    $model->detail_type=Advert::DETAIL_TYPE_AIRCRAFT;
                    $attributes['detail_type']='hidden';
                    $attributes['code.html.objectTypeAircraft']='<label>Aircraft</label>';
                }
                elseif($account->category == Account::CATEGORY_AIRPORT) {
                    $model->detail_type=Advert::DETAIL_TYPE_EQUIPMENT;
                    $attributes['detail_type']='hidden';
                    $attributes['code.html.objectTypeEquipment']='<label>Equipment</label>';
                }
                else {
                    if(!$model->detail_type) {
                        $model->detail_type=Advert::DETAIL_TYPE_AIRCRAFT;
                    }
                    $attributes['detail_type']=[
                        'type'=>'radioList',
                        'params'=>[
                            'data'=>[
                                Advert::DETAIL_TYPE_AIRCRAFT=>'Aircraft',
                                Advert::DETAIL_TYPE_EQUIPMENT=>'Equipment',
                            ],
                            'htmlOptions'=>[
                                'class'=>'js-detail-type',
                                'container'=>'div',
                                'labelOptions'=>['class'=>'inline', 'style'=>'font-weight:normal']
                            ]
                        ]
                    ];
                }
                
                $attributes['detail_type_value']=[
                    'type'=>'text',
                    'params'=>[
                        'hideLabel'=>true,
                        'htmlOptions'=>['class'=>'form-control w50 js-detail-type-value']
                    ]
                ];
                
                $attributes['code.html.objectTypeAlert']='<div class="alert alert-warning js-detail-type-alert" style="display:none">Вы сменили пользователя у объявления. Редактирование поля будет доступно после сохранения изменений.</div>';
                
                $attributes[]='category';
                $attributes['more_info']='textArea';
                $attributes['file']='common.ext.file.file';
                
                return $attributes;
            }
        ]
    ],
];