<?php
use common\components\helpers\HRequest as R;

return [
    'class'=>'\crud\models\ar\common\ext\parser\models\Page',
    'config'=>[
        'tablename'=>'parser_pages',
        'definitions'=>[
            'column.pk',
            'column.create_time',
            'process_id'=>'INT(11) NOT NULL, FOREIGN KEY `fk_process_id` (`process_id`) REFERENCES `parser_processes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE',
            'group_id'=>'INT(11) NOT NULL, FOREIGN KEY `fk_group_id` (`group_id`) REFERENCES `parser_groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE',
            'hash'=>['type'=>'VARCHAR(32), KEY(`hash`), UNIQUE `unq_page` (`process_id`, `hash`)', 'Уникальный хэш страницы в рамках одного процесса'],
            'status'=>['type'=>'INT(11)', 'label'=>'Статус'],
            'type'=>['type'=>'INT(11)', 'label'=>'Тип страницы'],
            'url'=>['type'=>'TEXT', 'label'=>'URL страницы'],
            'parent_page_id'=>['type'=>'INT(11) NOT NULL DEFAULT 0', 'label'=>'Идентификатор родительской страницы'],
            'pager_page_id'=>['type'=>'INT(11) NOT NULL DEFAULT 0', 'label'=>'Идентификатор страницы пагинации при постраничном парсинге'],
        ],
        'behaviors'=>[
            'statusBehavior'=>'\common\ext\parser\behaviors\StatusBehavior',
            'pageModelBehavior'=>'\common\ext\parser\behaviors\PageModelBehavior'
        ],
        'consts'=>[
            'TYPE_ENTRY'=>1,
            'TYPE_LINK'=>100,
            'TYPE_PAGINATION'=>200,
            'TYPE_CONTENT'=>300,
        ]
    ],
    'onBeforeLoad'=>function(){R::e404();}
];