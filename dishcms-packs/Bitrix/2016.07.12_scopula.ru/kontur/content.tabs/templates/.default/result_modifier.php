<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(!empty($arResult['TABS'])) {
	foreach($arResult['TABS'] as $key=>$arTab) {
		if(empty($arTab['FILE'])) continue;
		$arResult['TABS'][$key]['FILE']=$this->getComponent()->prepareFile($arTab['FILE'], $this->getFolder());
	}
}
?>
