<?php
use common\components\helpers\HRequest as R;

return [
    'class'=>'\crud\models\ar\accounts\models\AccountFavoriteProduct',
    'relations'=>[
        'accounts'=>[
            'type'=>'has_many',
            'attribute'=>'account_id'
        ]
    ],
    'config'=>[
        'tablename'=>'accounts_favorite_products',
        'definitions'=>[
            'column.pk',
            'foreign.account_id',
            'foreign.product_id',
            'column.create_time',
        ],
    ],
    'crud'=>[
        'onBeforeLoad'=>function() { R::e404(); }
    ]
];