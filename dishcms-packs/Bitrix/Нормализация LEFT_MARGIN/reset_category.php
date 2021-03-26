<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>

<?php

define('IBLOCK_ID', 15);

ini_set('memory_limit', '1024M');
set_time_limit(0);

if (!CModule::IncludeModule("sale") || !CModule::IncludeModule("catalog") || !CModule::IncludeModule("iblock")){
	return;
}

global $DB;

// $DB->Query("UPDATE `b_iblock_element` SET DETAIL_PICTURE = {$fileID} WHERE ID = {$arFields['ID']}");

function get_childs($sectionId) {
    $sections=[];

    $arFilter = array('IBLOCK_ID' => IBLOCK_ID, 'SECTION_ID' => $sectionId); // выберет потомков без учета активности
    $rsSect = CIBlockSection::GetList(array('left_margin' => 'asc'),$arFilter);

    while ($arSect = $rsSect->GetNext())
    {
        $sections[] = $arSect;
    }

    // get childs
    return $sections;
};

$normalize=function($sections, $left=1) use (&$normalize, $getChilds) {
	global $DB;

    if(!empty($sections)) 
    {

        foreach($sections as $section) {
            $section['LEFT_MARGIN']=$left;
            $section['RIGHT_MARGIN']=$left + 1;
            
            $childs=get_childs($section['ID']);
            if(!empty($childs)) {
                $section['RIGHT_MARGIN']=$normalize($childs, $section['RIGHT_MARGIN']) + 1;
                $left=$section['RIGHT_MARGIN'];
            }

            // $DB->Query("UPDATE `b_iblock_section` SET LEFT_MARGIN = {$section['LEFT_MARGIN']}, RIGHT_MARGIN = {$section['RIGHT_MARGIN']} WHERE ID = {$section['ID']}");

            $left=$section['RIGHT_MARGIN'] + 1;
        }

        return $section['RIGHT_MARGIN'];
    }
    return false;
};

$roots = [];

$arFilter = array('IBLOCK_ID' => IBLOCK_ID, 'SECTION_ID' => false);
$rsSect = CIBlockSection::GetList(array('left_margin' => 'asc'),$arFilter);

while ($arSect = $rsSect->GetNext())
{
    $roots[] = $arSect;
}

$normalize($roots);
