<?php
/**
 * История платежей (для API)
 */
use common\components\helpers\HYii as Y;
use common\components\helpers\HRequest as R;
use ykassa\components\helpers\HYKassa;
use ykassa\components\helpers\HYKassaHistory;
use ykassa\components\ApiConfig;

$historyDataProvider=['criteria'=>['condition'=>'1<>1']];

return [
    'class'=>'\crud\models\ar\ykassa\models\History',
    'config'=>[
        'tablename'=>'ykassa_payments',
        'definitions'=>[
            'column.pk',
            'column.create_time'=>['label'=>'Дата'],
            'column.update_time',
            'configuration_id'=>['type'=>'string', 'label'=>'Идетификатор конфигураци'],
            'status'=>['type'=>'string', 'label'=>'Статус'],
            'amount'=>['type'=>'DECIMAL(15,2) NOT NULL DEFAULT 0', 'label'=>'Сумма'],
            'payment_id'=>['type'=>'string', 'label'=>'Идетификатор платежа'],
            'payment_type'=>['type'=>'string', 'label'=>'Тип платежа'],
            'int_param_1'=>['type'=>'integer', 'label'=>'Параметр 1 (число)'],
            'int_param_2'=>['type'=>'integer', 'label'=>'Параметр 2 (число)'],
            'int_param_3'=>['type'=>'integer', 'label'=>'Параметр 3 (число)'],
            'string_param_1'=>['type'=>'string', 'label'=>'Параметр 1 (строка)'],
            'string_param_2'=>['type'=>'string', 'label'=>'Параметр 2 (строка)'],
            'string_param_3'=>['type'=>'string', 'label'=>'Параметр 3 (строка)'],
            'comment'=>['type'=>'string', 'label'=>'Комментарий'],
            'uuid'=>['type'=>'string', 'label'=>'Внутренний UUID'],
        ],
        'indexes'=>[
            'payment_id',
            'configuration_id',
            'int_param_1',
            'int_param_2',
            'int_param_3',
            'string_param_1',
            'string_param_2',
            'string_param_3',
            'uuid'
        ],
        'rules'=>[
            'safe',
            ['comment', 'safe']
        ],
        'methods'=>[
            function() {
                ob_start();?>                
				<?php 
                return ob_get_clean();
            }
        ]
    ],
    'menu'=>[
        'backend'=>['label'=>'Яндекс.Касса. История платежей']
    ],
    'buttons'=>[
        'create'=>['label'=>''],
        'custom'=>function() {
            echo \CHtml::tag('div', ['class'=>'row'], 
                \CHtml::tag('div', ['class'=>'col-md-12'], 
                    \CHtml::link('<i class="glyphicon glyphicon-cog"></i> Настройки Яндекс.Кассы', '/cp/settings/ykassa', ['class'=>'btn btn-warning'])
            ));
        }
    ],
    'crud'=>[
        'index'=>[
            'onBeforeLoad'=>function() use (&$historyDataProvider) {
                if(isset($_POST['showdetail'])) {
                    $html='<table class="table table-bordered" width="100%" style="margin-bottom:0">';
                    if(isset($_POST['id'])) {
                        HYKassa::checkPaymentStatus($_POST['id']);
                        if($payments=HYKassaHistory::getAllByPaymentId($_POST['id'])) {
                            $html.='<tr class="bg-info"><th>Дата</th><th>Статус</th></tr>';
                            foreach($payments as $payment) {
                                $html.='<tr><td>'.$payment->create_time.'</td>';
                                $html.='<td>'.HYKassa::getStatusTag($payment->status).'</td></tr>';
                            }
                        }
                    }
                    $html.='</table>';
                    echo $html;
                    exit;
                }

                $historyDataProvider=[
                    'criteria'=>['group'=>'payment_id'],
                    'sort'=>['defaultOrder'=>'`t`.`id` DESC']
                ];
            },
            'url'=>'/cp/crud/index',
            'title'=>'История платежей',
            'gridView'=>[
                'dataProvider'=>&$historyDataProvider,
                'emptyText'=>'Записей не найдено',
                'summaryText'=>'Платежи {start}-{end} из {count}',
                'columns'=>[
                    [
                        'name'=>'id',
                        'header'=>'#',
                        'headerHtmlOptions'=>['style'=>'width:5%'],
                    ],
                    [
                        'type'=>'raw',
                        'name'=>'payment_id',
                        'header'=>'Информация о платеже',
                        'value'=>function($data) {
                            $html=\CHtml::link("<strong>Платеж #{$data->payment_id}</strong>", 'javascript:;', ['class'=>'js-payment-detail', 'data-payment'=>$data->payment_id]) . '<br/><small>';
                            Y::js('payment-detail', '$(document).on("click", ".js-payment-detail", function(e) {
                                let paymentId=$(e.target).closest("a").data("payment");
                                let detailBox=$(e.target).parents("tr:first").siblings(".payment-detail[data-payment="+paymentId+"]");
                                if(detailBox.length>0) {
                                    if(detailBox.is(":hidden")) detailBox.show();
                                    else detailBox.hide();
                                }
                                else {
                                    $.post("/cp/crud/index?cid=ykassa_payments", {showdetail: 1, id: paymentId}, function(html) {
                                        $(e.target).parents("tr:first").after("<tr data-payment=\""+paymentId+"\" class=\"payment-detail\"><td class=\"bg-warning\" colspan=\"4\">"+html+"</td></tr>");
                                    });
                                } 
                            });', \CClientScript::POS_READY);
                            if($data->payment_id) {
                                // $html.="<b>ID</b>: $data->payment_id<br/>";                                
                            }
                            if($data->configuration_id) {
                                static $configs=[];
                                if(!array_key_exists($data->configuration_id, $configs)) {
                                    $configs[$data->configuration_id]=ApiConfig::load($data->configuration_id);
                                }
                                if(!empty($configs[$data->configuration_id])) {
                                    $info=$configs[$data->configuration_id]->get('crud_history_get_info', ['data'=>$data]);
                                    if(is_array($info)) {
                                        foreach($info as $title=>$text) {
                                            $html.="<b>{$title}</b>: $text<br/>";
                                        }
                                    }
                                    else {
                                        $html.=(string)$info;
                                    }
                                }
                            }

                            if($data->comment) {
                                // $html.="<b>Комментарий</b>: $data->comment<br/>";
                            }

                            return $html . '</small>';
                        }
                    ],
                    [
                        'name'=>'amount',
                        'header'=>'Сумма',
                        'headerHtmlOptions'=>['style'=>'width:20%;text-align:center'],
                        'htmlOptions'=>['style'=>'text-align:center'],
                        'value'=>'\common\components\helpers\HHtml::price($data->amount) . " руб."'
                    ],
                    /*
                    [
                        'name'=>'status',
                        'type'=>'raw',
                        'header'=>'Статус',
                        'headerHtmlOptions'=>['style'=>'width:20%;text-align:center'],
                        'htmlOptions'=>['style'=>'text-align:center'],
                        'value'=>'\ykassa\components\helpers\HYKassa::getStatusTag($data->status)'
                    ],
                    /**/
                    [
                        'name'=>'create_time',
                        'header'=>'Дата',
                        'headerHtmlOptions'=>['style'=>'width:20%;text-align:center'],
                        'htmlOptions'=>['style'=>'text-align:center'],
                        'value'=>'\common\components\helpers\HYii::formatDate($data->create_time)'
                    ],
                ]
            ]
        ],
        'create'=>[
            'onBeforeLoad'=>function(){R::e404();}
        ],
        'update'=>[
            'onBeforeLoad'=>function(){R::e404();}
        ],
        'delete'=>[
            'onBeforeLoad'=>function(){R::e404();}
        ]
    ]
];
