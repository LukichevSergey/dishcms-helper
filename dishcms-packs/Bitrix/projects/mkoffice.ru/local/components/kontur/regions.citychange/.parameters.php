<? 
/**
 * @link https://dev.1c-bitrix.ru/learning/course/?COURSE_ID=43&LESSON_ID=2132
 */
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'class.php');

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Localization\Loc,
Kontur\Core\Iblock\Component\Parameters,
Kontur\Core\Main\Tools\Data;

Loc::loadMessages(__FILE__);

$arComponentParameters = array(
    "GROUPS" => [
        'KRCC_SETTINGS'=>[
            'NAME'=>Loc::getMessage('KRCC_SETTINGS_GROUP'),
            'SORT'=>500
        ]
    ],
    "PARAMETERS" => [],
);

Parameters::addIblockParameters($arComponentParameters['PARAMETERS'], $arCurrentValues);

foreach(['LOCATION', 'ADDRESS', 'MAP', 'IS_DEFAULT'] as $code) {
    Parameters::addPropertyCode($arComponentParameters['PARAMETERS'], $arCurrentValues['IBLOCK_ID'], [
        'PARAM_NAME'=>"{$code}_PROPERTY_CODE", 
        'PARENT'=>'KRCC_SETTINGS',
        'NAME'=>Loc::getMessage("KRCC_{$code}_PROPERTY_CODE"),
        'REFRESH'=>'N',
        'MULTIPLE'=>'N',
        'SIZE'=>1
    ]);
}

$arComponentParameters['PARAMETERS']['YMAP_API_KEY']=[
    'PARENT'=>'ADDITIONAL_SETTINGS',
    'NAME'=>Loc::getMessage('KONTUR_REGIONS_CHANGECITY_YMAP_API_KEY'),
    'TYPE'=>'STRING'
];
$arComponentParameters['PARAMETERS']['COOKIE_KEY']=[
    'PARENT'=>'ADDITIONAL_SETTINGS',
    'NAME'=>Loc::getMessage('KONTUR_REGIONS_CHANGECITY_COOKIE_KEY'),
    'TYPE'=>'STRING'
];
$arComponentParameters['PARAMETERS']['DISABLE_BX_GEOIP']=[
    'PARENT'=>'ADDITIONAL_SETTINGS',
    'NAME'=>Loc::getMessage('KONTUR_REGIONS_CHANGECITY_DISABLE_BX_GEOIP'),
    'TYPE'=>'CHECKBOX',
    'DEFAULT'=>'N'
];
$arComponentParameters['PARAMETERS']['DISABLE_YANDEX_GEOIP']=[
    'PARENT'=>'ADDITIONAL_SETTINGS',
    'NAME'=>Loc::getMessage('KONTUR_REGIONS_CHANGECITY_DISABLE_YANDEX_GEOIP'),
    'TYPE'=>'CHECKBOX',
    'DEFAULT'=>'N'
];
?>
