<? 
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Localization\Loc,
    Kontur\Core\Iblock\Component\Parameters;

Loc::loadMessages(__FILE__);    

Parameters::addParameter($arTemplateParameters,'BX_POPUP_ID', [
    'PARENT'=>'KBTN_SETTINGS',
	'NAME'=>'Идентификатор всплывающего окна',
	'DEFAULT'=>'konturbxpopupwin'
]);

Parameters::addParameter($arTemplateParameters,'BX_POPUP_TITLE', [
    'PARENT'=>'KBTN_SETTINGS',
	'NAME'=>'Заголовок всплывающего окна',
	'DEFAULT'=>''
]);

Parameters::addParameter($arTemplateParameters,'BX_POPUP_BTN_OK_ENABLE', [
    'PARENT'=>'KBTN_SETTINGS',
	'NAME'=>'Отобразить кнопку "Да"',
	'TYPE'=>'CHECKBOX',
	'DEFAULT'=>'N',
    'REFRESH'=>'Y'
]);

if(isset($arCurrentValues['BX_POPUP_BTN_OK_ENABLE']) && ($arCurrentValues['BX_POPUP_BTN_OK_ENABLE'] == 'Y')) {
	Parameters::addParameter($arTemplateParameters,'BX_POPUP_BTN_OK_LABEL', [
    	'PARENT'=>'KBTN_SETTINGS',
		'NAME'=>'Подпись кнопки "Да"',
		'DEFAULT'=>'Да'
	]);
    
    Parameters::addParameter($arTemplateParameters,'BX_POPUP_BTN_OK_CSSCLASS', [
    	'PARENT'=>'KBTN_SETTINGS',
		'NAME'=>'CSS класс кнопки "Да"',
		'DEFAULT'=>'popup-window-button-accept'
	]);
    
    Parameters::addParameter($arTemplateParameters,'BX_POPUP_BTN_OK_CLICK', [
    	'PARENT'=>'KBTN_SETTINGS',
		'NAME'=>'Обработчик кнопки "Да"',
		'DEFAULT'=>''
	]);
}

Parameters::addParameter($arTemplateParameters,'BX_POPUP_BTN_CANCEL_LABEL', [
    'PARENT'=>'KBTN_SETTINGS',
	'NAME'=>'Подпись кнопки "Отмена"',
	'DEFAULT'=>'Отменить'
]);

Parameters::addParameter($arTemplateParameters,'BX_POPUP_BTN_CANCEL_CSSCLASS', [
    'PARENT'=>'KBTN_SETTINGS',
	'NAME'=>'CSS класс кнопки "Отмена"',
    'DEFAULT'=>'popup-window-button-cancel'
]);
?>
