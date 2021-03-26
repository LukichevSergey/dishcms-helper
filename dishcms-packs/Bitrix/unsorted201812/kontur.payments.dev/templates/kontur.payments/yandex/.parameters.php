<? 
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Localization\Loc,
    Kontur\Core\Iblock\Component\Parameters;

Loc::loadMessages(__FILE__);    

$arTemplateParameters['PRICE_DEFAULT']=[
    'PARENT'=>'ADDITIONAL_SETTINGS',
    'NAME'=>'Цена по умолчанию (руб)'
];

$arTemplateParameters['PRICE_PROMOCODE']=[
    'PARENT'=>'ADDITIONAL_SETTINGS',
    'NAME'=>'Цена по промокоду (руб)'
];

$arTemplateParameters['PRICE_CREDITOR']=[
    'PARENT'=>'ADDITIONAL_SETTINGS',
    'NAME'=>'Цена за дополнительный блок "Кредитор" (руб)'
];

Parameters::addIblockParameters(
    $arTemplateParameters, 
    $arCurrentValues,
    ['PARAM_NAME'=>'PROMOCODE_IBLOCK_TYPE', 'NAME'=>'Тип информационного блока "Сотрудники"'], 
    ['PARAM_NAME'=>'PROMOCODE_IBLOCK_ID', 'NAME'=>'Идентификатор инфоблока "Сотрудники"']
);

?>