1) 

Добавление алиасов
'aliases'=>array(
    'subscribe' => 'application.modules.subscribe'
),

Подключение модуля
'modules'=>array(
        'subscribe',
),

	'params'=>array(
        'UrlManagerHelper' => array(
        'modules' => array( ..., 'subscribe', ... ),
        ),

	),


use

<?php $this->widget('\subscribe\widgets\SubscribeWidget'); ?>








