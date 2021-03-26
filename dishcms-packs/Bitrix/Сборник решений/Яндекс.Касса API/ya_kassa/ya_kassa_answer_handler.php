<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>
<?php
require_once("config.php");
require_once('yandex-checkout-sdk/lib/autoload.php');
use YandexCheckout\Client;
$notice = json_decode(file_get_contents("php://input"), true);
$res_st = 'error';
if ($notice['event'] == 'payment.succeeded') {
	if ($notice['object']['status'] == 'succeeded') {
		$ya_id = $notice['object']['id'];
		CModule::IncludeModule('iblock');
		$db = CIBlockElement::GetList(['SORT'=>'ASC'], ['IBLOCK_ID'=> 25, 'PROPERTY_YA_INVOICE_ID' => $ya_id], false, false, ['ID','IBLOCK_ID','PROPERTY_SUM']);
		while($d = $db->GetNextElement()) {
			$f = $d->fields;
			//проверяем сумму
			if ($f['PROPERTY_SUM_VALUE'] == $notice['object']['amount']['value']) {
				//перепроверяем статус платежа в ЯК
				$client = new Client();
				$client->setAuth(YA_SHOP_ID, YA_SHOP_SECRET_KEY);
				$donate_info = $client->getPaymentInfo($ya_id);
				if ($donate_info->status == 'succeeded') {
					CIBlockElement::SetPropertyValuesEx($f['ID'], 25, ['STATUS'=>133]);
					$res_st = 'ok';
				}
			}
		}

	}
}



file_put_contents($_SERVER['DOCUMENT_ROOT'].'/ya_kassa/ya_log.txt', 'test(' . date('Y-m-d H:i:s') . ")======\n" . $notice['object']['id'] . ' | ' . $notice['object']['status'] . ' | ' . $res_st. "====\n", FILE_APPEND);