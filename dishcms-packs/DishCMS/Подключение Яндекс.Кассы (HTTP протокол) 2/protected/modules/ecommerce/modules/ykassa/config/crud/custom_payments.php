<?php
use common\components\helpers\HRequest as R;

return [
    'class'=>'\crud\models\ar\ykassa\models\CustomPayment',
    'config'=>[
        'tablename'=>'ykassa_custom_payments',
        'definitions'=>[
            'column.pk',
            'column.create_time'=>['label'=>'Дата'],
            'column.update_time',
            'status'=>['type'=>'string', 'label'=>'Статус'],
            'sum'=>['type'=>'DECIMAL(15,2) NOT NULL DEFAULT 0', 'label'=>'Сумма'],
            'order_number'=>['type'=>'string', 'label'=>'Идетификатор оплаты'],
            'invoice_id'=>['type'=>'string', 'label'=>'Идентификатор транзакции в Яндекс.Кассе'],
            'payment_type'=>['type'=>'string', 'label'=>'Тип способа оплаты'],
            'name'=>['type'=>'string', 'label'=>'Имя'],
            'phone'=>['type'=>'string', 'label'=>'Контактный телефон'],
            'email'=>['type'=>'string', 'label'=>'E-Mail'],
            'comment'=>['type'=>'TEXT', 'label'=>'Комментарий'],
            'ym_merchant_receipt'=>['type'=>'LONGTEXT', 'label'=>'Данные для чека'],
        ],
        'rules'=>[
            'safe',
            ['name, phone, email, sum', 'required'],
            ['email', 'email'],
            ['sum', 'numerical', 'min'=>1, 'tooSmall'=>'Сумма должна быть больше 1 руб'],
            ['comment, payment_type', 'safe']
        ],
        'methods'=>[
            function() {
                ob_start();?>
                public function beforeSave() {
					parent::beforeSave();
					if($this->isNewRecord) {
						$this->order_number=\common\components\helpers\HHash::u('P');
						$this->status=\ykassa\components\helpers\HYKassa::STATUS_NEW;
					}
					return true;
				}
				<?php 
                return ob_get_clean();
            }
        ]
    ],
    'menu'=>[
        'backend'=>['label'=>'Яндекс.Касса']
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
            'url'=>'/cp/crud/index',
            'title'=>'История платежей',
            'gridView'=>[
                'dataProvider'=>['sort'=>['defaultOrder'=>'id DESC']],
                'summaryText'=>'Платежи {start}-{end} из {count}',
                'columns'=>[
                    [
                        'name'=>'id',
                        'header'=>'#',
                        'headerHtmlOptions'=>['style'=>'width:5%'],
                    ],
                    [
                        'type'=>'column.title',
                        'header'=>'Оплата',
                        'attributeTitle'=>'order_number',
                        'info'=>[
                            'Транзакция'=>'$data->invoice_id',
                            'Имя'=>'$data->name',
                            'E-Mail'=>'$data->email',
                            'Телефон'=>'\common\components\helpers\HTools::formatPhone($data->phone)',
                            'Комментарий'=>'$data->comment'
                        ]
                    ],
                    [
                        'name'=>'sum',
                        'header'=>'Сумма',
                        'headerHtmlOptions'=>['style'=>'width:20%;text-align:center'],
                        'htmlOptions'=>['style'=>'text-align:center'],
                        'value'=>'\common\components\helpers\HHtml::price($data->sum) . " руб."'
                    ],
                    [
                        'name'=>'status',
                        'type'=>'raw',
                        'header'=>'Статус',
                        'headerHtmlOptions'=>['style'=>'width:20%;text-align:center'],
                        'htmlOptions'=>['style'=>'text-align:center'],
                        'value'=>'\ykassa\components\helpers\HYKassa::getStatusTag($data->status)'
                    ],
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
            'onBeforeLoad'=>function(){R::redirect('/cp/crud/index?cid=ykassa_custom_payments');}
        ],
        'delete'=>[
            'onBeforeLoad'=>function(){R::e404();}
        ]
    ]
];