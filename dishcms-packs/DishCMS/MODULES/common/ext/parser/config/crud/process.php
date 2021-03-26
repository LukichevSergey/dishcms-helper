<?php
use common\components\helpers\HRequest as R;

return [
    'class'=>'\crud\models\ar\common\ext\parser\models\Process',
    'config'=>[
        'tablename'=>'parser_processes',
        'definitions'=>[
            'column.pk',
            'column.create_time',
            'status'=>['type'=>'INT(11)', 'label'=>'Статус'],
            'config_hash'=>['type'=>'TEXT', 'label'=>'Хэш конфигурации'],
            'iteration'=>['type'=>'INT(11) UNSIGNED NOT NULL DEFAULT 0', 'label'=>'Номер последней итерации'],
            'process_hash'=>['type'=>'VARCHAR(32)', 'label'=>'Хэш процесса'],
            'pid'=>['type'=>'INT(11)', 'label'=>'Идентификатор процесса PHP скрипта'],
            'last_execute_time'=>['type'=>'DATETIME', 'label'=>'Дата последнего запуска процесса'],
            'is_periodic'=>['type'=>'TINYINT(1) NOT NULL DEFAULT 0', 'label'=>'Процесс является периодическим (для регулярного парсинга)'],
            'start_time'=>['type'=>'DATETIME', 'label'=>'Дата и время первого запуска процесса (для регулярного парсинга)'],
            'duration'=>['type'=>'INT(11)', 'label'=>'Период запуска в секундах (для регулярного парсинга)'],
        ],
        'behaviors'=>[
            'statusBehavior'=>'\common\ext\parser\behaviors\StatusBehavior',
            'processModelBehavior'=>'\common\ext\parser\behaviors\ProcessModelBehavior'
        ]
    ],
    'onBeforeLoad'=>function(){R::e404();}
];