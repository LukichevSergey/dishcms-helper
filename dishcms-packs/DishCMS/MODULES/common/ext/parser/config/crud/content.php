<?php
use common\components\helpers\HRequest as R;

return [
    'class'=>'\crud\models\ar\common\ext\parser\models\Content',
    'config'=>[
        'tablename'=>'parser_contents',
        'definitions'=>[
            'column.pk',
            'column.create_time',
            'page_id'=>'INT(11) NOT NULL, FOREIGN KEY `fk_page_id` (`page_id`) REFERENCES `parser_pages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE',
            'text'=>'LONGTEXT'
        ],
        'behaviors'=>[
            'contentModelBehavior'=>'\common\ext\parser\behaviors\ContentModelBehavior',
        ]
    ],
    'onBeforeLoad'=>function(){R::e404();}
];