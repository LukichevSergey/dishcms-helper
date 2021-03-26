<?php
//file_put_contents(dirname(__FILE__).'/check.log', var_export($_POST, true), FILE_APPEND);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require \Bitrix\Main\Application::getDocumentRoot() . getLocalPath('components/kontur.payments/class.php');
	
use Bitrix\Main\Loader,
Bitrix\Main\Localization\Loc,
Kontur\Core\Main\Tools\Data,
Kontur\Core\Main\Tools\Request;

Loc::loadMessages(__FILE__);
Loader::includeModule("iblock");

	include('config.php');

	$hash = md5($_POST['action'].';'.$_POST['orderSumAmount'].';'.$_POST['orderSumCurrencyPaycash'].';'.$_POST['orderSumBankPaycash'].';'.$configs['shopId'].';'.$_POST['invoiceId'].';'.$_POST['customerNumber'].';'.$configs['ShopPassword']);		
	if (strtolower($hash) != strtolower($_POST['md5'])){
		\KonturPaymentsComponent::updateStatus(['IBLOCK_ID'=>66, "EVENT_TYPE"=>"KONTUR_PAYMENT_FORM", "EVENT_ID"=>"29",], $_POST['orderNumber'], 12);
		$code = 1;
	}
	else {
		$code = 0;
	}
		print '<?xml version="1.0" encoding="UTF-8"?>';
		print '<checkOrderResponse performedDatetime="'. $_POST['requestDatetime'] .'" code="'.$code.'"'. ' invoiceId="'. $_POST['invoiceId'] .'" shopId="'. $configs['shopId'] .'"/>';

?>
