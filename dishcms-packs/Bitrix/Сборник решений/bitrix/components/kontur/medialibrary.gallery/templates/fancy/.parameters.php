<? 
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Localization\Loc,
    Kontur\Core\Iblock\Component\Parameters,
    Kontur\Core\Main\Tools\Data;

Loc::loadMessages(__FILE__);    

Parameters::addParameter($arTemplateParameters, 'FANCYBOX_CLASS', [
    'NAME'=>'CSS класс для инициализации скрипта (fancybox)',
    'DEFAULT'=>'gallery-images'
]);

Parameters::addParameter($arTemplateParameters, 'FANCYBOX_REL', [
    'NAME'=>'Имя группы изображений (fancybox:rel)',
    'DEFAULT'=>'gallery-images'
]);

Parameters::addParameter($arTemplateParameters, 'FANCYBOX_SCRIPT_DISABLE', [
    'NAME'=>'Отключить инициализацию скрипта (fancybox)',
    'TYPE'=>'CHECKBOX',
    'DEFAULT'=>'N'
]);

?>
