<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

require \Bitrix\Main\Application::getDocumentRoot() . getLocalPath('components/kontur.payments/class.php');

function setErrorStatus() 
{
	\CHTTP::SetStatus("404 Not Found");
	@define("ERROR_404","Y");
	die;
}

if(!preg_match('/^order=.*?$/i', $_SERVER['QUERY_STRING'])) {
	setErrorStatus();
}

$order=preg_replace('/^order=(.*?)$/i', '$1', $_SERVER['QUERY_STRING']);
$arOrderParams=\KonturPaymentsComponent::decrypt(urldecode($order));

if(empty($arOrderParams['IBLOCK_ID']) || empty($arOrderParams['ORDER_ID'])) {
	setErrorStatus();
}

$arResult['ORDER']=\KonturPaymentsComponent::getOrder(['IBLOCK_ID'=>$arOrderParams['IBLOCK_ID'], 'ID'=>$arOrderParams['ORDER_ID']]);

if(empty($arResult['ORDER']) || ($arResult['ORDER']['ID']!=$arOrderParams['ORDER_ID'])) {
	setErrorStatus();
}

$arParams['SUM']=$arResult['ORDER']['PROPERTIES']['AMOUNT']['VALUE'];
?>
