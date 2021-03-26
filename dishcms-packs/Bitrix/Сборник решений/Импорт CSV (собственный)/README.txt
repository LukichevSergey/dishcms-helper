На основе импорта филиалов

1) Добавить в init.php (либо поправить файл /php_interface/kontur/autoload.php)
require_once \Bitrix\Main\Application::getDocumentRoot() . getLocalPath('php_interface/kontur/autoload.php');
