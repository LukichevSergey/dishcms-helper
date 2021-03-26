<?php

$arResult['ITEMS']=CIBlockElement::GetList(
	Array("SORT"=>"ASC"),
	Array("PROPERTY_manufacturer"=>$_REQUEST['brand'], 'ACTIVE'=>'Y'),
	Array("SECTION_ID"),
	//$GLOBALS[$arParams['ELEMENT_FILER_NAME']] ?: Array(),
	//$GLOBALS[$arParams['ELEMENT_GROUP_NAME']] ?: Array(),
	false,
 	Array()
);
var_dump($arResult['ITEMS']);
?>
