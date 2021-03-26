<?php
return [
    'import'=>[
        'secure'=>['\ecommerce\modules\moysklad\models\Import', 'secure'],
        'create'=>['\ecommerce\modules\moysklad\models\Import', 'start'],
        'next'=>['\ecommerce\modules\moysklad\models\Import', 'next']
    ]
];