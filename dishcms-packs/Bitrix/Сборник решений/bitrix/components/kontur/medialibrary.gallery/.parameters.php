<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Localization\Loc,
    Kontur\Core\Iblock\Component\Parameters;

Loc::loadMessages(__FILE__);    

$arComponentParameters = ['GROUPS'=>[
	'KMLIBGALLERY_SETTINGS'=>[
		'NAME'=>Loc::getMessage('MLIBGALLERY_SETTINGS_GROUP'),
	]
]];

Parameters::addMedialibParameter($arComponentParameters['PARAMETERS'], 'MLIBCOLLECTION_ID', [
    'PARENT'=>'KMLIBGALLERY_SETTINGS'
]);

Parameters::addParameter($arComponentParameters['PARAMETERS'], 'TMB_WIDTH', [
    'PARENT'=>'KMLIBGALLERY_SETTINGS',
    'NAME'=>'Ширина превью-изображения (px)'
]);

Parameters::addParameter($arComponentParameters['PARAMETERS'], 'TMB_HEIGHT', [
    'PARENT'=>'KMLIBGALLERY_SETTINGS',
    'NAME'=>'Высота превью-изображения (px)'
]);
?>