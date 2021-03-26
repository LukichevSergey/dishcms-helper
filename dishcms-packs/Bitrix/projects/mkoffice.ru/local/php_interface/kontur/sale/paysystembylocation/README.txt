Подключение в init.php
require_once dirname(__FILE__) . '/kontur/sale/paysystembylocation/LocationPayRestriction.php';
LocationPayRestriction::registerEvent($createPaySystemTable=false);

$createPaySystemTable при инициализации установить в true для создания таблицы сохранения местоположения платежных систем, затем вернуть в false
