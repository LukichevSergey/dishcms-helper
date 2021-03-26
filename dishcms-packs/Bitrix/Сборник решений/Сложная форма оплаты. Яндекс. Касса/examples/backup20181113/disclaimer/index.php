<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle('Заявление об отказе от взаимодействия');
?>
<?php $APPLICATION->IncludeComponent(
	"kontur.payments", 
	"yandex", 
	array(
		"COMPONENT_TEMPLATE" => "yandex",
		"IBLOCK_TYPE" => "requests",
		"IBLOCK_ID" => "66",
		"PROMOCODE_IBLOCK_TYPE" => "dynamic_data",
		"PROMOCODE_IBLOCK_ID" => "9",
		"PAYMENT_TEST_MODE" => "Y",
		"PAYMENT_STATUS_WAIT_ID" => "10",
		"PAYMENT_STATUS_PAID_ID" => "11",
		"PAYMENT_STATUS_FAIL_ID" => "12",
		"EVENT_TYPE" => "KONTUR_PAYMENT_FORM",
		"EVENT_ID" => "29",
		"PRICE_DEFAULT" => "1000",
		"PRICE_PROMOCODE" => "300",
		"PRICE_CREDITOR" => "50"
	),
	false
); ?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
