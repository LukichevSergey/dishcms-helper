<?
/**
 * Требуется для версий 1С-Битрикс. Т.к. к версии 16.5.5 существует проблема 
 * при включенных вложенных ЧПУ, если символьные коды элементов в разных 
 * разделах совпадают.
 */
\CModule::IncludeModule("iblock");

$CATALOG_URLREWRITE_IBLOCK_TYPE='catalog';
$CATALOG_URLREWRITE_IBLOCK_ID=1;//$arParams['IBLOCK_ID'];
$uri=urldecode(preg_replace('#\?.*$#', '', $_SERVER['REQUEST_URI']));
if(preg_match_all('#[^/?]+#', $uri, $codes) && !empty($codes[0]) && ($codes[0][0]=='catalog') && (count($codes[0]) > 1)) {
	unset($codes[0][0]);
	$codes=$codes[0];

	$fGetSection=function($CODE, $SECTION_ID=null) use ($CATALOG_URLREWRITE_IBLOCK_TYPE, $CATALOG_URLREWRITE_IBLOCK_ID) {
		$arFilter=array('IBLOCK_ID'=>$CATALOG_URLREWRITE_IBLOCK_ID, 'IBLOCK_TYPE'=>$CATALOG_URLREWRITE_IBLOCK_TYPE, 'ACTIVE'=>'Y', 'GLOBAL_ACTIVE'=>'Y', 'CODE'=>$CODE);
		if(!empty($SECTION_ID)) $arFilter['SECTION_ID']=$SECTION_ID;
		$rsSection=CIBlockSection::GetList(array('SORT'=>'ASC'), $arFilter, false, array('ID'));
		return $rsSection->Fetch();
	};

	$fGetElement=function($SECTION_ID, $CODE) use ($CATALOG_URLREWRITE_IBLOCK_TYPE, $CATALOG_URLREWRITE_IBLOCK_ID) {
		$arFilter=array('IBLOCK_ID'=>$CATALOG_URLREWRITE_IBLOCK_ID, 'IBLOCK_TYPE'=>$CATALOG_URLREWRITE_IBLOCK_TYPE, 'ACTIVE'=>'Y', 'GLOBAL_ACTIVE'=>'Y', 'CODE'=>$CODE, 'SECTION_ID'=>$SECTION_ID);
        $rsSection=CIBlockElement::GetList(array('SORT'=>'ASC'), $arFilter, false, false, array('ID'));
        return $rsSection->Fetch();
	};

	$remainingCount=count($codes);
	$iSectionID=null;
	$iElementID=null;
	foreach($codes as $code) {
		if(!($arNextSection=$fGetSection($code, $iSectionID))) {
			if($remainingCount > 1) {
				$iSectionID=$iElementID=null;
				break;
			}
			elseif(!empty($iSectionID)) {
				if($arElement=$fGetElement($iSectionID, $code)) {
					$iElementID=$arElement['ID'];
				}
			}
			else {
				$iSectionID=$iElementID=null;
				break;
			}
		}
		else {
			$iSectionID=(int)$arNextSection['ID'];
		}
		$remainingCount--;
	}
	
	if($iSectionID || $iElementID) {
		if($iElementID) $_REQUEST['urlrewrite_element_id']=$iElementID;
		// else $_REQUEST['SECTION_ID']=$iSectionID;
	}
}
?>
