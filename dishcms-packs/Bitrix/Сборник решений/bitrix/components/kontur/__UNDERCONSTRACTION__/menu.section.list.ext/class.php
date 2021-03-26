<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

class KonturMenuSectionListExtComponent extends CBitrixComponent
{
	/**
	 * @param array $arParams массив параметров компонента
	 * @param array $arExtParams массив дополнительных параметров компонента
	 * вида array(sOutParam=>sInParam), где sOutParam - имя параметра в выхдном массиве,
	 * sInParam префикс параметра в массиве $arParams.
	 */
	public function GetMenuItems($arParams, $arExtParams=array())
    {
		$arItems=array();

		$iCount=empty($arParams['MENU_ITEMS_COUNT']) ? 0 : (int)$arParams['MENU_ITEMS_COUNT'];
		for($i=1; $i<=$iCount; $i++) {
			$sName=$arParams['MENU_ITEMS_NAME_'.$i];
			if(!empty($sName)) {
				$arItem=array(
					'SORT' => (int)$arParams['MENU_ITEMS_SORT_'.$i],
					'NAME' => $sName,
					'LINK' => $arParams['MENU_ITEMS_LINK_'.$i],
					'SELECTED' => false
				);
				if(!empty($arExtParams)) {
					foreach($arExtParams as $sOutParam=>$sInParam) {
						$arItem[$sOutParam]=$arParams[$sInParam.$i];
					}
				}
				if(!empty($arParams['MENU_ITEMS_SECTIONS_'.$i])) {
					$arItem['SECTIONS'] = $this->GetSections((int)$arParams['MENU_ITEMS_IBLOCK_ID'], $arParams['MENU_ITEMS_SECTIONS_'.$i]);
					usort($arItem['SECTIONS'], function($a, $b) use (&$arItem) {
						if(!empty($a['SELECTED'])) $arItem['SELECTED']=true;
						if(!empty($b['SELECTED'])) $arItem['SELECTED']=true;
        		        return strcasecmp($a['NAME'], $b['NAME']);
			        });
				}
				$arItems[]=$arItem;
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

	public function GetSections($IBLOCK_ID, $arSectionId)
	{
		global $APPLICATION;
		
		$arSections=array();
		
    	if (\Bitrix\Main\Loader::includeModule('iblock')) {
        	$rsSection = CIBlockSection::GetTreeList(
            	array('IBLOCK_ID'=>$IBLOCK_ID, 'ID'=>$arSectionId, 'ACTIVE'=>'Y', 'GLOBAL_ACTIVE'=>'Y'),
	            array('ID', 'NAME', 'SECTION_PAGE_URL', 'DEPTH_LEVEL', 'LEFT_MARGIN')
    	    );
	        while($arSection = $rsSection->GetNext()) {
	        	if($APPLICATION->GetCurPage(false) == $arSection['SECTION_PAGE_URL']) {
	        		$arSection['SELECTED']=true;
	        	}
	        	else {
	        		$arSection['SELECTED']=false;
	        	}
	            $arSections[] = $arSection;
	        }
    	}
    	
		return $arSections;
	}
}

