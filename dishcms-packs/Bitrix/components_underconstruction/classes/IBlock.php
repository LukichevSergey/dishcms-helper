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
		while ($arIBlockType = $dbIBlockTypes->GetNext())
		{
		    $arIBlockTypes[$arIBlockType["ID"]] = $arIBlockType["ID"];
		}

		return $arIBlockTypes;
	}

	// Получение списка инфоблоков заданного типа для формы параметров
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

	// Получение элемента типа файл.
	public static function getFile($FILE_ID)
	{
		$dbFile = \CFile::GetByID($FILE_ID);
		return $dbFile->Fetch();
	}

	public static function getElement($ID, $propName=null)
	{
		return self::getItem(\CIBlockElement::GetByID($ID), $propName);
	}

	public static function getSection($ID, $propName=null)
    {
        return self::getItem(\CIBlockSection::GetByID($ID), $propName);
    }

	public static function getSectionByCode($IBLOCK_ID, $SECTION_CODE, $arSelect=array())
	{
		$arFilter = array("IBLOCK_ID"=>$IBLOCK_ID, "=CODE"=>$SECTION_CODE);
        $rsSection = \CIBlockSection::GetList(array(), $arFilter, false, $arSelect);

        return $rsSection->GetNext();
	}

	public static function getItem($dbResult, $propName=null)
	{
		$arResult = $dbResult->GetNext();
		if(($propName !== null) && $arResult)
            return $arResult[$propName];

		return $arResult;
	}

	public static function getList($dbResult)
	{
		$arResults = array();
		
		while($arResult = $dbResult->GetNext())
            $arResults[] = $arResult;

        return $arResults;
	}

	public static function getPropertyByCode($IBLOCK_ID, $ELEMENT_ID, $PROPERTY_CODE, $returnOnlyValue=true)
    {
		$VALUES = array();

        $dbProperty = \CIBlockElement::GetProperty($IBLOCK_ID, $ELEMENT_ID, array("sort" => "asc"), array("CODE"=>$PROPERTY_CODE));
		while ($arProperty = $dbProperty->GetNext()) {
	        $VALUES[] = $returnOnlyValue ? $arProperty['VALUE'] : $arProperty;
    	}

		return empty($VALUES) ? null : ((count($VALUES) == 1) ? reset($VALUES) : $VALUES);
    }

	 /**
     * Получение пользовательского свойства
     * @param string $IBLOCK_ID идентификатор инфоблока
     * @param string $SECTIN_ID идентификатор раздела
     * @param string $PROPERTY_NAME имя пользовательского свойства без префикса "UF_".
     * @return mixed Если свойство не найдено, возвращается NULL.
     */
    public static function getSectionUFProperty($IBLOCK_ID, $SECTIN_ID, $PROPERTY_NAME)
    {
        $dbSections=\CIBlockSection::GetList(Array("SORT"=>"ASC"), Array("IBLOCK_ID"=>$IBLOCK_ID, "ID"=>$SECTIN_ID),false, Array("UF_{$PROPERTY_NAME}"));
        return ($arSection=$dbSections->GetNext()) ? $arSection["UF_{$PROPERTY_NAME}"] : null;
    }

	public static function getSectionCount($IBLOCK_ID, $SECTION_ID, $GLOBAL_ACTIVE='Y')
	{
		$count =0;

		$dbSection = \CIBlockSection::GetList(
		    Array("sort"=>"asc", 'name'=>'asc'), 
		    Array(
		        'IBLOCK_ID'=>$IBLOCK_ID, 
		        'ID'=>$SECTION_ID,
		        'GLOBAL_ACTIVE'=>$GLOBAL_ACTIVE,
        		'CNT_ACTIVE'=>'Y'
		    ), 
		    true, 
		    array('NAME')
		);

		while($arSection = $dbSection->Fetch()) {
		    $count += $arSection["ELEMENT_CNT"];
		}

		return $count;
	}

	public static function GetElementsCount($arFilter, $arGroupBy=false, $arNavStartParams=false, $arSelectFields=array('ID'))
	{
        $dbElements = \CIBlockElement::GetList(
            Array("sort"=>"asc", 'name'=>'asc'),
			$arFilter,
            $arGroupBy,
			$arNavStartParams,
			$arSelectFields
        );

		return $dbElements->SelectedRowsCount();
	}
}
