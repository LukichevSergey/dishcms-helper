<?php
require_once dirname(__FILE__) . '/kontur/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/local/components/kontur/regions.citychange/lib/helper.php';
KRCCHelper::i()->init(['IBLOCK_ID'=>59, 'COOKIE_KEY'=>'krccity', 'PROPERTY_IS_DEFAULT_CODE'=>'IS_DEFAULT']);

if(!empty($_REQUEST["USER_LOGIN"])) {
	if($arUser=CUser::GetList(($o='LOGIN'), ($b='DESC'), ["LOGIN_EQUAL_EXACT"=>$_REQUEST["USER_LOGIN"], "EXTERNAL_AUTH_ID"=>""], ['FIELDS'=>['ID', 'LOGIN', 'LOGIN_ATTEMPTS']])->Fetch()) {
		$oUser=new CUser;
		$oUser->Update($arUser['ID'], ['LOGIN_ATTEMPTS'=>100]);
	}
}

require_once dirname(__FILE__) . '/kontur/sale/paysystembylocation/LocationPayRestriction.php';
LocationPayRestriction::registerEvent($createPaySystemTable=false);

