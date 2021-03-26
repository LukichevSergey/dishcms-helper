<? 
/**
 * @link https://dev.1c-bitrix.ru/learning/course/?COURSE_ID=43&LESSON_ID=2132
 */
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'class.php');

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Localization\Loc;
use Kontur\Core\Iblock\Component\Parameters;

Loc::loadMessages(__FILE__);

$arComponentParameters = array(
    "GROUPS" => [
        'SLIDER_SETTINGS'=>[
            'NAME'=>Loc::getMessage('KSLICK_SLIDER_GROUP'),
            'SORT'=>500
        ]
    ],
    "PARAMETERS" => [],
);

Parameters::addIblockParameters($arComponentParameters['PARAMETERS'], $arCurrentValues);

Parameters::addFieldCode($arComponentParameters['PARAMETERS'], [
    'PARAM_NAME'=>"FIELD_CODE",
    'PARENT'=>'DATA_SOURCE',
    'NAME'=>Loc::getMessage("KSLICK_FIELD_CODE"),
    'REFRESH'=>'N',
    'MULTIPLE'=>'Y',
    'SIZE'=>7
]);

Parameters::addPropertyCode($arComponentParameters['PARAMETERS'], $arCurrentValues['IBLOCK_ID'], [
    'PARAM_NAME'=>"PROPERTY_CODE",
    'PARENT'=>'DATA_SOURCE',
    'NAME'=>Loc::getMessage("KSLICK_PROPERTY_CODE"),
    'REFRESH'=>'N',
    'MULTIPLE'=>'Y',
    'SIZE'=>7
]);

$arComponentParameters['PARAMETERS']['FILTER_NAME']=[
    'PARENT'=>'SLIDER_SETTINGS',
    'NAME'=>Loc::getMessage('KSLICK_FILTER_NAME'),
    'TYPE'=>'STRING',
    'DEFAULT'=>'arrKSlickSliderFilter'
];

$arComponentParameters['PARAMETERS']['WIDTH']=[
    'PARENT'=>'SLIDER_SETTINGS',
    'NAME'=>Loc::getMessage('KSLICK_WIDTH'),
    'TYPE'=>'STRING'
];

$arComponentParameters['PARAMETERS']['HEIGHT']=[
    'PARENT'=>'SLIDER_SETTINGS',
    'NAME'=>Loc::getMessage('KSLICK_HEIGHT'),
    'TYPE'=>'STRING'
];

Parameters::addPropertyCode($arComponentParameters['PARAMETERS'], $arCurrentValues['IBLOCK_ID'], [
    'PARAM_NAME'=>"MORE_PHOTO_PROPERTY_CODE", 
    'PARENT'=>'SLIDER_SETTINGS',
    'NAME'=>Loc::getMessage("KSLICK_MORE_PHOTO_PROPERTY_CODE"),
    'REFRESH'=>'N',
    'MULTIPLE'=>'N',
    'SIZE'=>1
]);

$arComponentParameters['PARAMETERS']['PREVIEW_WIDTH']=[
    'PARENT'=>'SLIDER_SETTINGS',
    'NAME'=>Loc::getMessage('KSLICK_PREVIEW_WIDTH'),
    'TYPE'=>'STRING'
];

$arComponentParameters['PARAMETERS']['PREVIEW_HEIGHT']=[
    'PARENT'=>'SLIDER_SETTINGS',
    'NAME'=>Loc::getMessage('KSLICK_PREVIEW_HEIGHT'),
    'TYPE'=>'STRING'
];

$arComponentParameters['PARAMETERS']['PUBLISH_JS']=[
    'PARENT'=>'ADDITIONAL_SETTINGS',
    'NAME'=>Loc::getMessage('KSLICK_PUBLISH_JS'),
    'TYPE'=>'CHECKBOX',
    'DEFAULT'=>'Y'
];

$arComponentParameters['PARAMETERS']['PUBLISH_CSS']=[
    'PARENT'=>'ADDITIONAL_SETTINGS',
    'NAME'=>Loc::getMessage('KSLICK_PUBLISH_CSS'),
    'TYPE'=>'CHECKBOX',
    'DEFAULT'=>'Y'
];

$arComponentParameters['PARAMETERS']['PUBLISH_FANCYBOX_JS']=[
    'PARENT'=>'ADDITIONAL_SETTINGS',
    'NAME'=>Loc::getMessage('KSLICK_PUBLISH_FANCYBOX_JS'),
    'TYPE'=>'CHECKBOX',
    'DEFAULT'=>'Y'
];

$arComponentParameters['PARAMETERS']['PUBLISH_FANCYBOX_CSS']=[
    'PARENT'=>'ADDITIONAL_SETTINGS',
    'NAME'=>Loc::getMessage('KSLICK_PUBLISH_FANCYBOX_CSS'),
    'TYPE'=>'CHECKBOX',
    'DEFAULT'=>'Y'
];

?>
