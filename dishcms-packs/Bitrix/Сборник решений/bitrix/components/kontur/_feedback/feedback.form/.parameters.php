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
	'GROUPS'=>[
		'KFF_SETTINGS'=>[
			'NAME'=>Loc::getMessage('KFF_SETTINGS_GROUP'),
		],
	]
);

Parameters::addIblockParameters($arComponentParameters['PARAMETERS'], $arCurrentValues);

Parameters::addEventParameters($arComponentParameters['PARAMETERS'], $arCurrentValues);

$arComponentParameters['PARAMETERS']['KFF_FORM_ID']=[
    'PARENT'=>'KFF_SETTINGS',
    'NAME'=>Loc::getMessage('KFF_FORM_ID'),
    'DEFAULT'=>'KFF'
];

$arComponentParameters['PARAMETERS']['KFF_FORM_HASH']=[
    'PARENT'=>'KFF_SETTINGS',
    'NAME'=>Loc::getMessage('KFF_FORM_HASH'),
    'DEFAULT'=>md5(uniqid())
];

Parameters::addFieldCode($arComponentParameters['PARAMETERS'], [
    'PARAM_NAME'=>'KFF_FIELD_CODE', 
    'PARENT'=>'KFF_SETTINGS',
    'NAME'=>Loc::getMessage('KFF_FIELD_CODE'),
    'REFRESH'=>'Y'
]);

Parameters::addPropertyCode($arComponentParameters['PARAMETERS'], $arCurrentValues['IBLOCK_ID'], [
    'PARAM_NAME'=>'KFF_PROPERTY_CODE', 
    'PARENT'=>'KFF_SETTINGS',
    'NAME'=>Loc::getMessage('KFF_PROPERTY_CODE'),
    'REFRESH'=>'Y'
]);

if(!empty($arCurrentValues['KFF_FIELD_CODE']) || !empty($arCurrentValues['KFF_FIELD_CODE'])) 
{
    $idxField = \KonturFeedbackFormComponent::addFieldParameters(
        $arComponentParameters, 
        Data::get($arCurrentValues, 'KFF_FIELD_CODE', [])
    );
    \KonturFeedbackFormComponent::addFieldParameters(
        $arComponentParameters, 
        Data::get($arCurrentValues, 'KFF_PROPERTY_CODE', []), 
        \KonturFeedbackFormComponent::PROPERTY_PREFIX, 
        $idxField
    );
}

?>
