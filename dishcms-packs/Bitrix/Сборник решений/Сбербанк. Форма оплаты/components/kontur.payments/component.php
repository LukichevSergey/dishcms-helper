<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();


\Bitrix\Main\Loader::includeModule("iblock");

$arResult=[
    'PAYMENT_STATUS' => static::checkPayment($arParams)
];

$this->IncludeComponentTemplate();
?>