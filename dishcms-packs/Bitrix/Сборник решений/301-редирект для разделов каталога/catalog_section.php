<?php
// Разместить в компоненте catalog/section.php. В некотрых решения есть получение $arCurSection, разместить после получения данных для данной категории.
// иначе удалить.
if( !empty($arCurSection) && !isset($arResult['VARIABLES']['SECTION_CODE']) ) {
	if ( isset($arResult['VARIABLES']['SECTION_CODE_PATH']) && (strpos($arResult['VARIABLES']['SECTION_CODE_PATH'], '/') === false) ) {
		$dbNewSection=\CIBlockSection::GetList( array("SORT"=>"ASC"), array("CODE"=>$arResult['VARIABLES']['SECTION_CODE_PATH']) );
		if ( $arNewSection = $dbNewSection->GetNext() ) {
			if( isset($arNewSection['SECTION_PAGE_URL']) && $arNewSection['SECTION_PAGE_URL'] ) {
				LocalRedirect($arNewSection['SECTION_PAGE_URL'], false, "301 Moved permanently");
			}
		}
	}
	// Bitrix\Iblock\Component\Tools::process404('Page not found', true, true, false);
	if (!defined("ERROR_404")) define("ERROR_404", "Y");
	\CHTTP::setStatus("404 Not Found");
	if ($APPLICATION->RestartWorkarea()) {
   		require(\Bitrix\Main\Application::getDocumentRoot()."/404.php");
		die();
	}
}
