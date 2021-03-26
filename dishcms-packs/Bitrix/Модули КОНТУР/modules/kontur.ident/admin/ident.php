<?php
use Bitrix\Main\Loader;
use Kontur\Ident\Helper;

require_once($_SERVER["DOCUMENT_ROOT"] . '/bitrix/modules/main/include/prolog_admin_before.php');

/** @global CMain $APPLICATION */
$APPLICATION->SetTitle('Интеграция с IDENT');

require($_SERVER["DOCUMENT_ROOT"] . '/bitrix/modules/main/include/prolog_admin_after.php');

Loader::includeModule('kontur.ident');

try {
    Helper::checkAccess();
    $APPLICATION->IncludeComponent('kontur:ident.admin', '', [], false, ['HIDE_ICONS'=>'Y']);
}
catch(\Exception $e) {
    \CAdminMessage::ShowMessage($e->getMessage());
}

require($_SERVER["DOCUMENT_ROOT"] . '/bitrix/modules/main/include/epilog_admin.php');