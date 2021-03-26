<?
if(!function_exists("KonturGetSectionUFProp"))
{
    /**
     * Получение пользовательского свойства
     * @param string $IBLOCK_ID идентификатор инфоблока
     * @param string $SECTION_ID идентификатор раздела
     * @param string $PROPERTY_NAME имя пользовательского свойства без префикса "UF_".
     * @return mixed Если свойство не найдено, возвращается NULL.
     */
    function KonturGetSectionUFProp($IBLOCK_ID, $SECTION_ID, $PROPERTY_NAME, $bUseFetch=false)
    {
        $dbSections=CIBlockSection::GetList(Array("SORT"=>"ASC"), Array("IBLOCK_ID"=>$IBLOCK_ID, "ID"=>$SECTION_ID),false, Array("UF_{$PROPERTY_NAME}"));
		if($bUseFetch) $arSection=$dbSections->Fetch();
		else $arSection=$dbSections->GetNext();
        return $arSection ? $arSection["UF_{$PROPERTY_NAME}"] : null;
    }
}
