<?php
use common\components\helpers\HRequest as R;

return [
    'class'=>'\crud\models\ar\common\ext\parser\models\Parser',
    'access'=>[
        ['allow', 'users'=>['@'], 'roles'=>['admin', 'sadmin']]
    ],
    'config'=>[
        'tablename'=>'parser_parser',
        'definitions'=>[
            'column.pk'
        ],
        'behaviors'=>[
            'parserModelBehavior'=>'\common\ext\parser\behaviors\ParserModelBehavior'
        ]
    ],
    'crud'=>[
        'onBeforeLoad'=>function(){R::e404();},
        'controllers'=>[
            '\common\ext\parser\behaviors\ParserCrudAjaxControllerBehavior'
        ],
    ]
];