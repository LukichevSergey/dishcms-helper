<?php
/**
 * Дополнительные параметры платежа (для API)
 */
use common\components\helpers\HRequest as R;

return [
    'class'=>'\crud\models\ar\ykassa\models\PaymentExtra',
    'config'=>[
        'tablename'=>'ykassa_payment_extras',
        'definitions'=>[
            'column.pk',
            'payment_id'=>['type'=>'int', 'label'=>'Идетификатор платежа'],
            'confirmation_url'=>['type'=>'TEXT', 'label'=>'URL перенаправления на форму оплаты']
        ],
        'indexes'=>[
            'payment_id'=>['references_table'=>'ykassa_payments', 'references_columns'=>['id']]
        ],
    ],
    'crud'=>[
        'onBeforeLoad'=>function(){R::e404();}
    ]
];