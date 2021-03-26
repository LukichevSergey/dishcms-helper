<?php

$arResult['SECTIONS']=array();

$dbElements=CIBlockElement::GetList(
	Array("SORT"=>"ASC"),
	Array("PROPERTY_manufacturer"=>$_REQUEST['brand'], 'ACTIVE'=>'Y')
);

$arResult['ELEMENTS_COUNT']=$dbElements->SelectedRowsCount();
if(!empty($arResult['ELEMENTS_COUNT'])) {

$arSectionIDs = array();
$arItemsCount = array();
while($arElement = $dbElements->GetNext()) {
	$sectionId=$arElement['IBLOCK_SECTION_ID'];
	
	$arSectionIDs[]=$sectionId;

	if(empty($arItemsCount[$sectionId]))
		$arItemsCount[$sectionId]=0;
	$arItemsCount[$sectionId]++;
}

$dbSections = CIBlockSection::GetList(
	Array("NAME"=>"ASC"),
	Array("ID"=>$arSectionIDs)
);

$arResult["SECTIONS_COUNT"]=$dbSections->SelectedRowsCount();

while($arSection = $dbSections->GetNext()) {
	$arSection["ELEMENT_CNT"]=$arItemsCount[$arSection["ID"]];
	$arResult['SECTIONS'][]=$arSection;
}

}
?>
