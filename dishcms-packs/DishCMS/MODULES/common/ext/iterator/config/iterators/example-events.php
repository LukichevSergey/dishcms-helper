<?php
use common\components\helpers\HArray as A;

return [
    'onCommonExtIteratorGetSecureKeys'=>function($event) {
        $event->setParam('secures', A::m($event->getParam('secures', []), ['MySecureKey']));
    }
];
