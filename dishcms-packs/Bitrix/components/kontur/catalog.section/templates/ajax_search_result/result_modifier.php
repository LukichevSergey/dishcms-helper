<?php
if(empty($arParams["DB_ITEMS"])) {
	$arWords=explode(' ', preg_replace('/ +/', ' ', trim($_REQUEST['q'])));
	if(empty($arWords)) 
		die;

	if(count($arWords) == 1) {
		$arFilter=array('%ELEMENT_NAME'=>$arWords[0]);
	}
	else {
		$arFilter=array('LOGIC'=>'AND');
		foreach($arWords as $word) $arFilter[]=array('%ELEMENT_NAME'=>$word);
	}

	$dbProducts=CCatalogProduct::GetList(
		array('QUANTITY'=>"desc"),
		$arFilter
	);
}
else {
	$dbProducts=$arParams["DB_ITEMS"];
}

$isBasePrice=in_array('BASE', $arParams['PRICE_CODE']);

if(!$isBasePrice) 
	die;

$arResult['ITEMS']=array();
$i=0;
while(($arItem = $dbProducts->GetNext()) && ($i++ < 10)) { 
	$arItem['PRICE_BASE']=CPrice::GetBasePrice($arItem['ID']);
	//$arItem['PRICE']=CPrice::GetByID($arItem['PRICE_BASE']['PRODUCT_ID']);
//	$rs=CPrice::GetList(array(),array('PRODUCT_ID'=>$arItem['PRICE_BASE']['PRODUCT_ID']));//,'CATALOG_GROUP_NAME'=>$arParams['PRICE_CODE']));
//	$arItem['PRICE']=\Kontur\IBlock::getList($rs);
	$arItem['PRICE']=$arItem['PRICE_BASE'];
	$arDiscounts=CCatalogDiscount::GetDiscountByProduct($arItem['PRICE_BASE']['PRODUCT_ID']);
	$discountPrice=false;
	if($arDiscounts) {
		$arPrice=$arItem['PRICE_BASE'];
		$discountPrice = CCatalogProduct::CountPriceWithDiscount(
            $arPrice["PRICE"],
            $arPrice["CURRENCY"],
            $arDiscounts
        );
	}
	$arItem['PRICE_DISCOUNT']=$discountPrice;

	$arElement=\Kontur\IBlock::getElement($arItem['PRICE_BASE']['PRODUCT_ID']?:$arItem['ID']);
	$arItem['PREVIEW_TEXT']=$arElement['PREVIEW_TEXT'];
	$arItem['NAME']=$arElement['NAME'];
	$arItem['PROPERTIES']['PROP_MARKER']=$arElement['PROPERTIES']['PROP_MARKER'];
	$arItem['PROPERTIES']['PROP_NEW']=$arElement['PROPERTIES']['PROP_NEW'];
	$arItem['DETAIL_PAGE_URL']=$arElement['DETAIL_PAGE_URL'];
	$arItem['ID']=$arElement['ID'];

	$arItem['PREVIEW_PICTURE']['SRC']='';

	$sItemPictureId=null;
	if(!empty($arElement['PREVIEW_PICTURE'])) {
    	$sItemPictureId=$arElement['PREVIEW_PICTURE'];
	}
	elseif(!empty($arElement['DETAIL_PICTURE'])) {
    	$sItemPictureId=$arElement['DETAIL_PICTURE'];
	}

	$arItemResizeImage=null;
	if($sItemPictureId) {
    	$arItemFile=\Kontur\IBlock::getFile($sItemPictureId);
	    $arItemResizeImage=CFile::ResizeImageGet($arItemFile["ID"], Array("width"=>90, "height"=>90));
		$arItem['PREVIEW_PICTURE']['SRC']=$arItemResizeImage['src'];
	}

	$arResult['ITEMS'][]=$arItem;
}

//echo '<pre style="color:#fff">';
//var_dump($arResult);
//echo '</pre>';
/*
$arResult['SECTIONS']=array();

$arParams["ELEMENT_FILTER"]["ACTIVE"]="Y";

$dbElements=CIBlockElement::GetList(
	Array("SORT"=>"ASC"),
	$arParams["ELEMENT_FILTER"]
);

$arResult['ELEMENTS_COUNT']=$dbElements->SelectedRowsCount();

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
*/
?>
