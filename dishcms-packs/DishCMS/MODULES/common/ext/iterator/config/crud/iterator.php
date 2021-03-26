<?php
use common\components\helpers\HArray as A;
use common\components\helpers\HRequest as R;

return [
    'class'=>'\crud\models\ar\common\ext\iterator\models\Iterator',
    'access'=>[
        ['allow', 'users'=>['@'], 'roles'=>['admin', 'sadmin']]
    ],
    'config'=>[
        'tablename'=>'ext_iterator',
        'definitions'=>[
            'column.pk',            
        ],
    ],
    'crud'=>[
        'onBeforeLoad'=>function(){ throw R::e404(); },
        'controllers'=>[
            '\common\ext\iterator\behaviors\IteratorCrudAjaxControllerBehavior'
        ],
    ],
    'events'=>[
        // Event: onCommonExtIteratorGetSecureKeys получение дополнительных ключей шифрования
        'onCommonExtIteratorGetSecureKeys'=>function($event) {
            $event->params['secures']=A::m(A::get($event->params, 'secures', []), ['<my_security_key>']);
        }
    ]
];