<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

class KonturContentTabsComponent extends CBitrixComponent
{
	public function GetItems($arParams)
	{
		$arItems=array();

		$iCount=empty($arParams['TABS_COUNT']) ? 0 : (int)$arParams['TABS_COUNT'];
		for($i=1; $i<=$iCount; $i++) {
			if($arParams['TABS_ITEM_VISIBLE_'.$i] == 'Y') {
				$sName=$arParams['TABS_ITEM_NAME_'.$i];
				if(empty($sName)) continue;

				$sFile=$arParams['TABS_ITEM_FILE_'.$i];
				if(empty($sFile)) continue;

				$sFile=strtr($sFile, array(
					'#SITE_DIR#'=>SITE_DIR,
					'#SITE_TEMPLATE_PATH#'=>SITE_TEMPLATE_PATH
				));

				$arItems[]=array(
					'SORT' => (int)$arParams['TABS_ITEM_SORT_'.$i],
					'NAME' => $sName,
					'FILE' => $arParams['TABS_ITEM_FILE_'.$i]
				);
			}
		}

		if(!empty($arItems)) {
			usort($arItems, function($a, $b) {
				if($a['SORT'] < $b['SORT']) return -1;
				elseif($a['SORT'] > $b['SORT']) return 1;
				return 0;
			});
		}

		return $arItems;
	}
	
	public function PrepareFile($sFile, $templateDir=null)
	{
		$sFile=strtr($sFile, array(
			'#SITE_DIR#'=>SITE_DIR,
		    '#SITE_TEMPLATE_PATH#'=>SITE_TEMPLATE_PATH
		));
		
		if(!is_file($sFile) && !empty($templateDir)) {
			$sFile=$templateDir . DIRECTORY_SEPARATOR . $sFile;
		}
		
		return $sFile;
	}
}
