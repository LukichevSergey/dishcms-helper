<? 
/**
 * @link https://dev.1c-bitrix.ru/learning/course/?COURSE_ID=43&LESSON_ID=2132
 */
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Localization\Loc,
    Kontur\Core\Iblock\Component\Parameters;

Loc::loadMessages(__FILE__);    

$arComponentParameters = ['GROUPS'=>[
	'KFBTN_SETTINGS'=>[
		'NAME'=>Loc::getMessage('KFBTN_SETTINGS_GROUP'),
	]
]];

Parameters::addParameter($arComponentParameters['PARAMETERS'],'FEEDBACK_URL',	[
	'NAME'=>Loc::getMessage('FEEDBACK_URL_NAME'),
	'DEFAULT'=>'/feedback/'
]);

Parameters::addParameter($arComponentParameters['PARAMETERS'], 'BTN_LABEL', [
	'NAME'=>Loc::getMessage('BTN_LABEL_NAME'),
	'DEFAULT'=>Loc::getMessage('BTN_LABEL_DEFAULT')
]);
?>
