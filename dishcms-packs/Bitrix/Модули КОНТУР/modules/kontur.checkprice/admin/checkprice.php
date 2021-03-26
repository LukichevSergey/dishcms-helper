<?php
use Bitrix\Main\Loader;
use Kontur\CheckPrice\Helper;

require_once($_SERVER["DOCUMENT_ROOT"] . '/bitrix/modules/main/include/prolog_admin_before.php');

/** @global CMain $APPLICATION */
$APPLICATION->SetTitle('Список изменения цен');

require($_SERVER["DOCUMENT_ROOT"] . '/bitrix/modules/main/include/prolog_admin_after.php');

Loader::includeModule('kontur.checkprice');

try {
    Helper::checkAccess();
    $APPLICATION->IncludeComponent('kontur:checkprice', '', [], false, ['HIDE_ICONS'=>'Y']);
}
catch(\Exception $e) {
    \CAdminMessage::ShowMessage($e->getMessage());
}

require($_SERVER["DOCUMENT_ROOT"] . '/bitrix/modules/main/include/epilog_admin.php');