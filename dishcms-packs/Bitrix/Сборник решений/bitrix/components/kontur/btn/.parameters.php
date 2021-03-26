<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Localization\Loc,
    Kontur\Core\Iblock\Component\Parameters;

Loc::loadMessages(__FILE__);    

$arComponentParameters = ['GROUPS'=>[
	'KBTN_SETTINGS'=>[
		'NAME'=>Loc::getMessage('KONTUR_BTN_SETTINGS_GROUP'),
	]
]];

Parameters::addParameter($arComponentParameters['PARAMETERS'], 'LINK', [
    'PARENT'=>'KBTN_SETTINGS',
	'NAME'=>Loc::getMessage('KONTUR_BTN_LINK_NAME'),
	'DEFAULT'=>''
]);

Parameters::addParameter($arComponentParameters['PARAMETERS'], 'LABEL', [
    'PARENT'=>'KBTN_SETTINGS',
	'NAME'=>Loc::getMessage('KONTUR_BTN_LABEL_NAME'),
	'DEFAULT'=>''
]);

Parameters::addParameter($arComponentParameters['PARAMETERS'], 'CSS_CLASS', [
    'PARENT'=>'KBTN_SETTINGS',
	'NAME'=>Loc::getMessage('KONTUR_BTN_CSS_CLASS_NAME'),
	'DEFAULT'=>''
]);
?>