<?php
// cron task /path/to/php/bin/php -f /path/to/webroot/local/php_interface/update_product_quantity.php > /dev/null 2>&1
$_SERVER["DOCUMENT_ROOT"]=dirname(__FILE__) . '/../..'; // если файл расположен в WEBROOT/local/php_interface
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
$_REQUEST['h']='<hash>';
\kontur\handlers\UpdateCatalogQuantityHandler::updateAll('<hash>');