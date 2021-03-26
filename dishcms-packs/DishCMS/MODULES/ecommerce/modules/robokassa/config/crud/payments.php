<?php
use common\components\helpers\HRequest as R;
use ecommerce\modules\robokassa\components\helpers\HRobokassa;

return [
    'class'=>'\crud\models\ar\robokassa\models\Payment',
    'config'=>[
        'tablename'=>'ecommerce_robokassa_payments',
        'definitions'=>[
            'column.pk',
            'column.create_time'=>['label'=>'Дата'],
            'column.update_time',
            'status'=>['type'=>'string', 'label'=>'Статус'],
            'sum'=>['type'=>'DECIMAL(15,2) NOT NULL DEFAULT 0', 'label'=>'Сумма'],
            'payment_id'=>['type'=>'string', 'label'=>'Идентификатор платежа на сайте'],
            'description'=>['type'=>'string', 'label'=>'Описание'],
            'name'=>['type'=>'string', 'label'=>'Имя'],
            'phone'=>['type'=>'string', 'label'=>'Контактный телефон'],
            'email'=>['type'=>'string', 'label'=>'E-Mail'],
            'comment'=>['type'=>'TEXT', 'label'=>'Комментарий'],
            'merchant_receipt'=>['type'=>'LONGTEXT', 'label'=>'Данные для чека'],
            'history'=>['type'=>'LONGTEXT', 'label'=>'Лог операций'],
        ],
        'rules'=>[
            'safe',
            ['payment_id, sum', 'required'],
            ['email', 'email'],
            ['sum', 'numerical', 'min'=>1, 'tooSmall'=>'Сумма должна быть больше 1 руб'],
            ['name, phone, email, comment', 'safe'],
        ],
        'methods'=>[
            function() {
                ob_start();?>
                public static function modelByPaymentId($id) { return static::model()->findByAttributes(["payment_id"=>$id]); }
                public static function modelByInvoiceId($id) { return static::model()->findByPk($id); }
                public function getPaymentId() { return $this->payment_id; }
                public function getInvoiceId() { return $this->id; }
                public function getShps() { return ['item'=>$this->id]; }
                public function getSum() { return (int)sprintf("%0.2f", $this->sum); }
                public function getDescription() { return mb_substr($this->description, 0, 100); }
                public function addHistory($date=null) {
                    $history=$this->getHistoryData();
                    $history[]=[
                        'date'=>$date ?: date('d.m.Y H:i:s'),
                        'status'=>$this->status,
                        'label'=>\ecommerce\modules\robokassa\components\helpers\HRobokassa::getStatusLabel($this->status)
                    ];
                    $this->setHistory($history);
                }
                public function setHistory($history) {
                    $this->history=is_array($history) ? json_encode($history, JSON_UNESCAPED_UNICODE) : $history;
                }
                public function getHistoryData() {
                    if($this->history && is_string($this->history)){ $this->history=json_decode($this->history, true); }
                    return is_array($this->history) ? $this->history : [];
                }
                public function beforeSave() {
					parent::beforeSave();
					if($this->isNewRecord) {
						$this->status=\ecommerce\modules\robokassa\components\helpers\HRobokassa::STATUS_NEW;
                        $this->addHistory();
					}
                    return true;
				}
                public function updateStatus($status, $date=null) {
                    if($this->status != $status) {
                        $this->status=$status;
                        $this->addHistory($date);
                        $this->update(['status', 'history']);
                    }                    
                }
				<?php 
                return ob_get_clean();
            }
        ]
    ],
    'menu'=>[
        'backend'=>['label'=>'Робокасса']
    ],
    'buttons'=>[
        'create'=>['label'=>''],
        'settings'=>['id'=>'robokassa', 'label'=>'Настройки', 'htmlOptions'=>['style'=>'margin-bottom:20px']],
    ],
    'crud'=>[
        'index'=>[
            'url'=>'/cp/crud/index',
            'title'=>'История платежей',
            'gridView'=>[
                'dataProvider'=>['sort'=>['defaultOrder'=>'id DESC']],
                'emptyText'=>'Платежей не найдено',
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
                        'attributeTitle'=>'description',
                        'info'=>[
                            'Транзакция'=>'$data->payment_id',
                            'Имя'=>'$data->name',
                            'E-Mail'=>'$data->email',
                            'Телефон'=>'\common\components\helpers\HTools::formatPhone($data->phone)',
                            'Комментарий'=>'$data->comment',
                        ]
                    ],
                    [
                        'name'=>'sum',
                        'header'=>'Сумма',
                        'headerHtmlOptions'=>['style'=>'width:15%;text-align:center'],
                        'htmlOptions'=>['style'=>'text-align:center'],
                        'value'=>'\common\components\helpers\HHtml::price($data->sum) . " руб."'
                    ],
                    [
                        'name'=>'status',
                        'type'=>'raw',
                        'header'=>'Статус',
                        'headerHtmlOptions'=>['style'=>'width:20%;text-align:center'],
                        'htmlOptions'=>['style'=>'text-align:center'],
                        'value'=>function($data) {
                            HRobokassa::checkStatus($data);
                            $html=HRobokassa::getStatusTag($data->status);
                            if($historyData=$data->getHistoryData()) {
                                $html.='<br/><small style="font-size:10px"><b>история изменений:</b><div style="width:100%;display:flex;flex-flow:wrap;flex-direction:column-reverse;">';
                                foreach($historyData as $history) {
                                    $html.="<div style=\"display:flex;width:100%;justify-content:space-between\"><div>{$history['date']}</div><div>{$history['label']}</div></div>";
                                }
                                $html.='</div></small>';
                            }
                            return $html;
                        }
                    ],
                    [
                        'name'=>'create_time',
                        'header'=>'Дата',
                        'headerHtmlOptions'=>['style'=>'width:10%;text-align:center'],
                        'htmlOptions'=>['style'=>'text-align:center;'],
                        'value'=>'\common\components\helpers\HYii::formatDate($data->create_time)'
                    ],
                ]
            ]
        ],
        'create'=>[
            'onBeforeLoad'=>function(){R::e404();}
        ],
        'update'=>[
            'onBeforeLoad'=>function(){R::redirect('/cp/crud/index?cid=robokassa_payments');}
        ],
        'delete'=>[
            'onBeforeLoad'=>function(){R::e404();}
        ]
    ]
];