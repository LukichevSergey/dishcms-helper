<? 
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Localization\Loc,
    Kontur\Core\Iblock\Component\Parameters;

Loc::loadMessages(__FILE__);    
    
Parameters::addIblockParameters(
    $arTemplateParameters, 
    $arCurrentValues, 
    ['PARAM_NAME'=>'IBLOCK_TYPE_2', 'NAME'=>'Тип информационного блока "Подать заявку"'], 
    ['PARAM_NAME'=>'IBLOCK_ID_2', 'NAME'=>'Идентификатор инфоблока "Подать заявку"']
);

Parameters::addEventParameters(
	$arTemplateParameters, 
	$arCurrentValues,
    ['PARAM_NAME'=>'EVENT_TYPE_2', 'NAME'=>'Тип почтового события "Подать заявку"'],
    ['PARAM_NAME'=>'EVENT_ID_2', 'NAME'=>'Шаблон уведомления "Подать заявку"']
);

?>
