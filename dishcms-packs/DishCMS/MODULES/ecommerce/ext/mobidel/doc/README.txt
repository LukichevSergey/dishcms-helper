1) В /protected/config/params.php добавить настройки
'ecommerce'=>['ext'=>[
	'mobidel'=>[
		'user'=>'',
		'password'=>'',
		'wid'=>''
	]
]];

2) В /DOrder/config/events.php
use ecommerce\ext\mobidel\components\helpers\HMobidel;
...
HMobidel::sendOrder($order);
...

3) Добавить поля в форму заказа вместо address
delivery_street
delivery_home
delivery_room
delivery_building
delivery_entrance
delivery_floor

