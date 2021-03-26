1) Подключить модуль в extend/config/main.php
	'modules'=>[
		...
		'bitrix24'=>['class'=>'\extend\modules\bitrix24\Bitrix24Module'],
	],
	
2) Параметры подключения в params
'params'=>[
	...
	'bitrix24'=>[
		'url'=>'https://адрес.bitrix24.ru',
		'user_id'=>идентификатор_пользователя,
	    'webhook'=>'код_вебхука'
	]
]

----------------------------------------------------------------------------------
Пример создания лида:
----------------------------------------------------------------------------------
use common\components\helpers\HTools; 
use extend\modules\bitrix24\components\helpers\HBitrix24;
...
HBitrix24::createLid([
	'TITLE'=>'Новая заявка с сайта',
	'NAME'=>фио,
	'EMAIL_WORK'=>электронная_почта,
	'PHONE_MOBILE'=>HTools::formatPhone(HTools::normalizePhone(номер_телефона)),
	'COMMENTS'=>'Комментарий',
	'STATUS_ID'=>'NEW',
	'SOURCE_ID'=>'WEB',
	'ASSIGNED_BY_ID'=>ИД_ОТВЕСТВЕННОГО
]);
