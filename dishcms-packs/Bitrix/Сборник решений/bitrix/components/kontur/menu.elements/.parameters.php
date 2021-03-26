<? 
/**
 * @link https://dev.1c-bitrix.ru/learning/course/?COURSE_ID=43&LESSON_ID=2132
 */
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Kontur\Core\Iblock\Component\Parameters;

Parameters::addIblockParameters($arComponentParameters['PARAMETERS'], $arCurrentValues);

Parameters::addParameter($arComponentParameters['PARAMETERS'], 'SEF_BASE_URL', [
    'PARENT'=>'URL_TEMPLATES',
	'NAME'=>Loc::getMessage('KONTUR_SEF_BASE_URL_NAME'),
	'DEFAULT'=>''
]);

Parameters::addParameter($arComponentParameters['PARAMETERS'], 'NAME_CODE', [
	'NAME'=>Loc::getMessage('KONTUR_NAME_CODE_NAME'),
	'DEFAULT'=>'NAME'
]);

Parameters::addParameter($arComponentParameters['PARAMETERS'], 'NAME_ALTERNATIVE_CODE', [
	'NAME'=>Loc::getMessage('KONTUR_NAME_ALTERNATIVE_CODE_NAME'),
	'DEFAULT'=>''
]);


?>
