<?php 
/**
 * Пример работы итератора с параметрами
 */
return [
    'iterator'=>[
        'secure'=>function() { return 'MySecureKey'; },
        'create'=>function($iteratorProcess) {
            return ['step'=>10];
        },        
        'next'=>function($iteratorProcess) {
            $iteratorProcess->setParam('percent', (int)$iteratorProcess->getParam('percent', 0) + (int)$iteratorProcess->getDataParam('step', 10));
            return $iteratorProcess->getParam('percent');
        }
    ],
];
