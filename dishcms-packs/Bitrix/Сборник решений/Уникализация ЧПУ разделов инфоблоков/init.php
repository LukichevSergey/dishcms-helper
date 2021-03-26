<?php
/**/
function s1SetUniqueSectionCode(&$arParams, $isNew=false)
{
    if( strlen($arParams["CODE"]) > 0 ) 
    {
	$arFilter = array( "CODE" => $arParams["CODE"] );
        if( !$isNew ) {
    	    $arFilter["!ID"] = $arParams["ID"];
	}
	
	$code = $arParams["CODE"];
	$i = 2;
	while((CIBlockSection::GetCount($arFilter) > 0) && ($i < 50)) {
	    $arFilter["CODE"] = $arParams["CODE"] = $code . "-" . $i++;
	}
	if ($i >= 50) {
	    $arParams["CODE"] = $code . "-" . time();
	}
    }
}

AddEventHandler("iblock", "OnBeforeIBlockSectionAdd", "s1OnBeforeIBlockSectionAddHandler");
function s1OnBeforeIBlockSectionAddHandler(&$arParams)
{
    s1SetUniqueSectionCode($arParams, true);
}

AddEventHandler("iblock", "OnBeforeIBlockSectionUpdate", "s1OnBeforeIBlockSectionUpdateHandler");
function s1OnBeforeIBlockSectionUpdateHandler(&$arParams)
{
    s1SetUniqueSectionCode($arParams, false);
}

// обновление символьных кодов разделов
/*
if( isset($_GET['updatesectioncode']) ) 
{
    CModule::IncludeModule("iblock");
    $bs = new CIBlockSection;
    $rs = CIBlockSection::GetList(array("SORT"=>"ASC"), array(), false, array("ID", "CODE"));
    while($ar = $rs->Fetch()) {
	$bs->Update($ar["ID"], array("CODE"=>$ar["CODE"]));
    }
}
/**/
?>
