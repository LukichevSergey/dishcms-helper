<?php
namespace Bitrix\Kontur\Core\IBlock;

class IBlock 
{
	public static function GetIBlockTypes()
	{
		return \CIBlockParameters::GetIBlockTypes();
	}
	
	public static function GetIBlockNames($SITE_ID, $arCurrentValues=array())
	{
		$arIBlocks=array();
		$db_iblock = \CIBlock::GetList(array("SORT"=>"ASC"), array(
			"SITE_ID"=>$SITE_ID,
			"TYPE" => (($arCurrentValues["IBLOCK_TYPE"] != "-") ? $arCurrentValues["IBLOCK_TYPE"] : "")
		));
		while($arRes = $db_iblock->Fetch())
			$arIBlocks[$arRes["ID"]] = $arRes["NAME"];
	
		return $arIBlocks;
	}
	
}