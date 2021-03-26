<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(!empty($arResult['TABS'])) {
	foreach($arResult['TABS'] as $key=>$arTab) {
		if(empty($arTab['FILE'])) {
			unset($arResult['TABS'][$key]);
			continue;
		}
		
		$sFile=$this->getComponent()->prepareFile($arTab['FILE'], $this->getFolder());
		ob_start();
		include($_SERVER['DOCUMENT_ROOT'] . $sFile);
		$sFileContent=ob_get_contents();
		ob_end_clean();
		if(!trim($sFileContent)) {
			unset($arResult['TABS'][$key]);
			continue;
		}
		
		$arResult['TABS'][$key]['FILE']=$sFile;
		$arResult['TABS'][$key]['CONTENT']=$sFileContent;
	}
}
?>
