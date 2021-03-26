<?php
namespace Kontur;

if (!\CModule::IncludeModule("iblock"))
{
    ShowMessage(GetMessage("IBLOCK_ERROR"));
    return false;
}

class IBlock
{
	// Получение списка типов инфоблоков
	public static function getIBlockTypesList()
	{
		$arIBlockTypes = array();

		$dbIBlockTypes = \CIBlockType::GetList(array("SORT"=>"ASC"), array("ACTIVE"=>"Y"));
		while ($arIBlockTypes = $dbIBlockTypes->GetNext())
		{
		    $arIBlockTypes[$arIBlockTypes["ID"]] = $arIBlockTypes["ID"];
		}

		return $arIBlockTypes;
	}

	// Получение списка инфоблоков заданного типа
	public static function getIBlocksList($IBLOCK_TYPE)
	{
		$arIBlocks = array();
		
		$dbIBlocks = \CIBlock::GetList(
		    array(
        		"SORT"  =>  "ASC"
		    ),
		    array(
        		"ACTIVE"    =>  "Y",
		        "TYPE"      =>  $IBLOCK_TYPE
		    ));
		while ($arIBlocks = $dbIBlocks->GetNext())
		{
		    $arIBlocks[$arIBlocks["ID"]] = "[" . $arIBlocks["ID"] . "] " . $arIBlocks["NAME"];
		}

		return $arIBlocks;
	}
}