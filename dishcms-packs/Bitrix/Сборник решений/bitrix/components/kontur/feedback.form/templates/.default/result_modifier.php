<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(!empty($_REQUEST['hash']) && ($_REQUEST['hash'] == $arParams['KFF_FORM_HASH'])) {
	$this->getComponent()->send($_REQUEST['data'], $arParams);
}