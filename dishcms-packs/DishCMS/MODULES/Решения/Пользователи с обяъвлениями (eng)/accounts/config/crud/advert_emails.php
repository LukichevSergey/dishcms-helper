<?php
use common\components\helpers\HYii as Y;
use common\components\helpers\HRequest as R;

return [
    'class'=>'\crud\models\ar\accounts\models\AdvertEmail',
    'relations'=>[
        'accounts_adverts'=>[
            'type'=>'belongs_to',
            'attribute'=>'advert_id',
            'titleAttribute'=>'id',
        ]
    ],
    'config'=>[
        'tablename'=>'accounts_advert_emails',
        'definitions'=>[
            'column.pk',
            'column.create_time',
            'column.title'=>['label'=>'Заголовок'],
            'body'=>['type'=>'LONGTEXT', 'label'=>'Текст сообщения'],
            'to'=>['type'=>'VARCHAR(255)', 'label'=>'E-Mail получателя'],
            'status'=>['type'=>'INT(11)', 'label'=>'Статус'],
            'foreign.advert_id',
        ],
        'rules'=>[
            'safe',
            ['status', 'safe']
        ],
        'consts'=>[
            'STATUS_SENDED'=>1,
            'STATUS_FAIL'=>500,            
        ],
        'methods'=>[
            'public static function add($advertId, $emailTemplateId, $params, $sended, $to) {
                $advertEmail=new \crud\models\ar\accounts\models\AdvertEmail;
                $advertEmail->title=\accounts\components\helpers\HAccountEmail::processShortCodes($emailTemplateId, \accounts\components\helpers\HAccountEmail::getSubject($emailTemplateId), $params);
                $advertEmail->body=\accounts\components\helpers\HAccountEmail::processShortCodes($emailTemplateId, \accounts\components\helpers\HAccountEmail::getBody($emailTemplateId), $params);
                $advertEmail->advert_id=$advertId;
                $advertEmail->to=$to;
                $advertEmail->status=$sended ? \crud\models\ar\accounts\models\AdvertEmail::STATUS_SENDED : \crud\models\ar\accounts\models\AdvertEmail::STATUS_FAIL;
                return $advertEmail->save();
            }'
        ]
    ],
    'buttons'=>[
        'create'=>['label'=>''],
    ],
    'crud'=>[
        'index'=>[
            'url'=>'/cp/crud/index',
            'title'=>'Почтовые уведомления',
            'gridView'=>[
                'dataProvider'=>[
                    'criteria'=>['select'=>'id, create_time, title, advert_id, status, `to`'],
                    'sort'=>['defaultOrder'=>'`create_time` DESC']
                ],
                'summaryText'=>'Почтовые уведомления {start} - {end} из {count}',
                'columns'=>[
                    'column.id',
                    [
                        'name'=>'create_time',
                        'header'=>'Дата',
                        'type'=>'raw',
                        'headerHtmlOptions'=>['style'=>'width:10%;text-align:center;white-space:nowrap;'],
                        'htmlOptions'=>['style'=>'width:10%;text-align:center;'],
                        'value'=>function($data) {
                            return Y::formatDate($data->create_time);
                        }
                    ],
                    [
                        'name'=>'title',
                        'header'=>'Заголовок',
                        'type'=>'raw',
                        'headerHtmlOptions'=>['style'=>'width:75%;text-align:center;white-space:nowrap;'],
                        'htmlOptions'=>['style'=>'width:75%;'],
                        'value'=>function($data) {
                            Y::jsCore('fancybox');
                            Y::js('advert_view_link', '$(document).on("click",".js-advert-view-link",function(e){
$.post("/accounts/admin/default/viewAdvertEmailBody",{id:$(e.target).data("id")},function(r){if(r.success){$.fancybox.open("<div class=\"message\" style=\"padding:40px\">" + r.data.body + "</div>");}},"json")});', \CClientScript::POS_READY);
                            return \CHtml::link($data->title, 'javascript:;', ['class'=>'js-advert-view-link', 'data-id'=>$data->id])
                                . '<br/><small><b>E-Mail получателя:</b> ' . ($data->to ?: 'не определен') . '</small>';
                        }
                    ],                    
                    [
                        'name'=>'status',
                        'header'=>'Статус',
                        'type'=>'raw',
                        'headerHtmlOptions'=>['style'=>'width:10%;text-align:center;white-space:nowrap;'],
                        'htmlOptions'=>['style'=>'width:10%;'],
                        'value'=>function($data) {
                            if($data->status == $data::STATUS_SENDED) {
                                return \CHtml::tag('span', ['class'=>'label label-success'], 'Отправлено');
                            }
                            else {
                                return \CHtml::tag('span', ['class'=>'label label-danger'], 'Не отправлено');
                            }
                        }
                    ],  
                ]
            ]
        ],
        'create'=>[
            'onBeforeLoad'=>function(){R::e404();},
            'url'=>'/cp/crud/create',
        ],
        'update'=>[
            'onBeforeLoad'=>function(){R::e404();},
            'url'=>'/cp/crud/update',
        ],
        'delete'=>[
            'url'=>'/cp/crud/delete',
        ],
    ],
];