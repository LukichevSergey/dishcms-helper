<?
if (!function_exists("KonturGetElementDetailPageUrl")) {
    function KonturGetElementDetailPageUrl($arItem)
    {
		$url=array('catalog');
		$rsGroup=CIBlockElement::GetElementGroups($arItem['ID'], true, array("ID", "CODE"));
		$group=$rsGroup->Fetch();

		$rs=CIBlockSection::GetNavChain($arItem['IBLOCK_ID'], $group["ID"], array("ID", "CODE"));
		while($ar=$rs->ExtractFields('nav_')) $url[]=$ar["CODE"]; 

		$url[]=$arItem["CODE"]; 
		return '/' . implode('/', $url) . '/';
	}
}