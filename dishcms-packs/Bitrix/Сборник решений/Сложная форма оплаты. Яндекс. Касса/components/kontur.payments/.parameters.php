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
    "GROUPS" => [],
    "PARAMETERS" => [],
);

Parameters::addIblockParameters($arComponentParameters['PARAMETERS'], $arCurrentValues);

Parameters::addEventParameters($arComponentParameters['PARAMETERS'], $arCurrentValues);

$arComponentParameters['PARAMETERS']['PAYMENT_TEST_MODE']=[
    'PARENT'=>'ADDITIONAL_SETTINGS',
    'NAME'=>Loc::getMessage('PARAMETER_PAYMENT_TEST_MODE'),
    'TYPE'=>'CHECKBOX'
];

foreach(['WAIT', 'PAID', 'FAIL'] as $status) {
    $arComponentParameters['PARAMETERS']['PAYMENT_STATUS_'.$status.'_ID']=[
        'PARENT'=>'ADDITIONAL_SETTINGS',
        'NAME'=>Loc::getMessage('PARAMETER_PAYMENT_STATUS_'.$status.'_NAME')
    ];
    
}
?>