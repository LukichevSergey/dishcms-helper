<?php
use common\components\helpers\HArray as A;
use ecommerce\modules\moysklad\models\Import;

return [
    'onCommonExtIteratorGetSecureKeys'=>function($event) {
        $event->params['secures']=A::m(A::get($event->params, 'secures', []), [Import::secure()]);
    }
];