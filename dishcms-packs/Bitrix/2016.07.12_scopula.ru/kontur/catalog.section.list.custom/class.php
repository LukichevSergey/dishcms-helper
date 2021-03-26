<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

class KonturCatalogSectionListCustomComponent extends CBitrixComponent
{
	public function GetItems($arParams)
	{
		$arItems=array();

		if(!empty($arParams["SECTIONS"])) {
			$rsSections=CIBlockSection::GetList(
  				array("NAME"=>"ASC"),
    			array("IBLOCK_ID"=>$arParams["IBLOCK_ID"], "ID"=>$arParams["SECTIONS"], "ACTIVE"=>"Y", "GLOBAL_ACTIVE"=>"Y"),
    			array("CNT_ACTIVE"=>"Y"),
	    		array("ID", "NAME", "SECTION_PAGE_URL", "DETAIL_PICTURE", "PICTURE")
			);
			while($arSection=$rsSections->GetNext()) {
				$arItems[]=$arSection;
			}
		}

		return $arItems;
	}
}
