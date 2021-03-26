<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(!empty($_REQUEST['hash']) && ($_REQUEST['hash'] == $arParams['KFF_FORM_HASH'])) {
	$component=$this->getComponent();
	
	$typeName=$component->getFormId($arParams) . '[TYPE]';
	$type=array_filter($_REQUEST['data'], function($v) use ($typeName) { return ($v['name'] == $typeName); });
	if(!empty($type) && ($type[0]['value'] == 'REQUEST')) {
		$arParams['IBLOCK_TYPE']=$arParams['IBLOCK_TYPE_2'];
		$arParams['IBLOCK_ID']=$arParams['IBLOCK_ID_2'];
		$arParams['EVENT_TYPE']=$arParams['EVENT_TYPE_2'];
		$arParams['EVENT_ID']=$arParams['EVENT_ID_2'];
	}
	
	$this->getComponent()->send($_REQUEST['data'], $arParams);
}