<?php
use common\components\helpers\HRequest as R;

return [
    'class'=>'\crud\models\ar\common\ext\parser\models\Group',
    'config'=>[
        'tablename'=>'parser_groups',
        'definitions'=>[
            'column.pk',
            'column.create_time',
            'process_id'=>'INT(11) NOT NULL, FOREIGN KEY `fk_process_id` (`process_id`) REFERENCES `parser_processes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE',
            'code'=>['type'=>'VARCHAR(64)', 'label'=>'Символьный код группы'],
            'path'=>['type'=>'VARCHAR(255)', 'label'=>'Путь к группе'],
            'depth'=>['type'=>'INT(11)', 'label'=>'Глубина вложенности'],
            'parent_group_id'=>['type'=>'INT(11) NOT NULL DEFAULT 0', 'label'=>'Идентификатор родительской группы'],
        ],
        'behaviors'=>[
            'groupModelBehavior'=>'\common\ext\parser\behaviors\GroupModelBehavior'
        ]        
    ],
    'onBeforeLoad'=>function(){R::e404();}
];