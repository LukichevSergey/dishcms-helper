<?php
/**
 * Пример конфигурации итератора
 * 
 * В одном файле может бытьЗадано несколько различных конфигураций.
 */
return [
    // конфигурация задается как array(id=>config), где
    // id (string) внутреннний идентификатор конфигурации
    // config (array) параметры конфигурации
    'example'=>[
        // "secure" (string|callable) дополнительный ключ шифрования данных.
        // Может быть передана callable функция без параметров, которая будет возвращать
        // дополнительный ключ шифрования 
        'secure'=>'',
        
        // "var_hash" (string) имя переменной в которой передается хэш процесса 
        // По умолчанию "h".
        'var_hash'=>'h',
        
        // "var_params" (string) имя переменной в которой передается хэш процесса 
        // По умолчанию "ipm".
        'var_params'=>'ipm',
        
        // "create" (callable) функция создания нового процесса
        // Может быть задана как массив [ИмяКласса, ИмяМетода].
        // @param \common\ext\iterator\models\Process $process модель процесса итератора
        // @return [] дополнительные данные для следующей итерации
        'create'=>function(\common\ext\iterator\models\Process $process) {
            // {{{ пример использования дополнительных данных для итерации
            \Yii::app()->user->setState('example_iteration_percent', 0);
            return ['step'=>10];
            // }}}
        },
        
        // "next" (callable) функция запуска новой итерации процесса
        // Может быть передан как массив [ИмяКласса, ИмяМетода].
        // Если не задан, то запуск новой итерации не произойдет.
        // Параметры функции запуска новой итерации процесса:
        // @param \common\ext\iterator\models\Process $process модель процесса итератора
        // @return (int|false) функция должна возвращать процент выполненности процесса. 
        // Значение равное 100 (ста) или более считается флагом завершения процесса.
        // Строгое значение false означает возникновение ошибки при выполнении итерации.
        'next'=>function(\common\ext\iterator\models\Process $process) {
            // {{{ пример использования дополнительных данных для итерации
            $percent=(float)\Yii::app()->user->getState('example_iteration_percent', 0) + $process->getDataParam('step', 10);
            \Yii::app()->user->setState('example_iteration_percent', $percent);
            return $percent;
            // }}}
        },
        
        // дополнительные события
        'events'=>[
            // "onAfterCreate" (callable) запускается после создания нового процесса  
            // @param \common\ext\iterator\models\Process $process модель процесса итератора
            'onAfterCreate'=>function(\common\ext\iterator\models\Process $process) {
                
            },
            
            // "onBeforeNext" (callable) запускается перед запуском второй и более итерации
            // @param \common\ext\iterator\models\Process $process модель процесса итератора
            'onBeforeNext'=>function(\common\ext\iterator\models\Process $process) {
                
            },
            
            // "onAfterDone" (callable) запускается после завершения процесса
            // @param \common\ext\iterator\models\Process $process модель процесса итератора
            'onAfterDone'=>function(\common\ext\iterator\models\Process $process) {
                
            }
        ]
    ]
];