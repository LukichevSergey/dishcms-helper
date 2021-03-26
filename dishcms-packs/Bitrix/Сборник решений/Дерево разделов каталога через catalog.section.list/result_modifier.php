<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arSectionsByDepth=array();
foreach ($arResult["SECTIONS"] as $arSection) {
	$arSectionsByDepth[ (int)$arSection["DEPTH_LEVEL"] ][ $arSection["ID"] ] = $arSection;
}

if(!function_exists('prepare_catalog_menu_section_tree')) {
	function prepare_catalog_menu_section_tree(&$arTree, $arSectionsByDepth, $parentSectionId=null, $depth=1) {
		if(isset($arSectionsByDepth[$depth])) {
			foreach($arSectionsByDepth[$depth] as $sectionId=>$arSection) {
				if(!$parentSectionId || ($parentSectionId === (int)$arSection['IBLOCK_SECTION_ID'])) {
					$sectionId=(int)$sectionId;
					$arTree[$sectionId]=$arSection;
					$arTree[$sectionId]["SUBSECTIONS"]=array();
					prepare_catalog_menu_section_tree($arTree[$sectionId]["SUBSECTIONS"], $arSectionsByDepth, $sectionId, $depth + 1);
				}
			}
		}
	}
}

$arResult["NEW_TREE"]=array();
prepare_catalog_menu_section_tree($arResult["NEW_TREE"], $arSectionsByDepth);
