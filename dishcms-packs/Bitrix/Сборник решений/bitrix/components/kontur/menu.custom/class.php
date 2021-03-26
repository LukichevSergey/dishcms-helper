<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Kontur\Core\Iblock\SectionTable,
    Kontur\Core\Main\Tools;

class KonturMenuCustomComponent extends CBitrixComponent
{
    const TYPE_LINK='L';
    const TYPE_MENU='M';
    const TYPE_SECTIONS='S';
    
	/**
	 * @param array $arParams массив параметров компонента
	 */
	public function getMenuItems($arParams)
    {
		$items=[];

		$count=Tools\Data::get($arParams, 'MENU_ITEMS_COUNT', 0);
		for($i=1; $i<=$count; $i++) {
			if($name=trim($arParams['MENU_ITEMS_NAME_'.$i])) {
                $type=trim($arParams['MENU_ITEMS_TYPE_'.$i]) ?: self::TYPE_LINK;
                $data=[
                    'NAME' => $name,
                    'TYPE' => $type,
                    'SORT' => (int)$arParams['MENU_ITEMS_SORT_'.$i],
                    'LINK' => trim($arParams['MENU_ITEMS_LINK_'.$i])
                ];
                
                $data['ITEMS']=[];
                if($type == self::TYPE_SECTIONS) {
                    $data['DEPTH_LEVEL'] = (int)$arParams['MENU_ITEMS_DEPTH_'.$i];
                    $data['IBLOCK_TYPE'] = trim($arParams['MENU_ITEMS_IBLOCK_TYPE_'.$i]);
                    $data['IBLOCK_ID'] = (int)$arParams['MENU_ITEMS_IBLOCK_ID_'.$i];
                    
                    if($data['IBLOCK_TYPE'] && $data['IBLOCK_ID']) {
                        // $filter=['=ACTIVE'=>'Y', '=GLOBAL_ACTIVE'=>'Y'];
                        // if($data['DEPTH_LEVEL'] > 0) {
                        //    $filter['>=DEPTH_LEVEL']=$data['DEPTH_LEVEL'];
                        // }
                        // $data['ITEMS']=Section::getTree($data['IBLOCK_ID'], ['filter'=>['=ACTIVE'=>'Y', '=GLOBAL_ACTIVE'=>'Y']]);
                        $data['ITEMS']=$this->getSections($data['IBLOCK_ID'], $data['DEPTH_LEVEL']);
                    }
                }
                elseif($type == self::TYPE_MENU) {
                    $data['DEPTH_LEVEL'] = (int)$arParams['MENU_ITEMS_DEPTH_'.$i];
                    $data['MENU_TYPE'] = trim($arParams['MENU_ITEMS_MENU_TYPE_'.$i]);
                    $data['ITEMS'] = $this->getMenuTypeItems($data['MENU_TYPE'], $data['DEPTH_LEVEL']);
                }
                
                $items[]=$data;
            }
        }

		if(!empty($items)) {
			usort($items, function($a, $b) {
				if($a['SORT'] < $b['SORT']) return -1;
				elseif($a['SORT'] > $b['SORT']) return 1;
				return 0;
			});
		}

		return $items;
    }
    
    public function getMenuTypeItems($menuType, $depth=0)
    {
        global $APPLICATION;
        // var_dump($menuType);
        $menu = $APPLICATION->GetMenu($menuType);
        // var_dump($menu->GetMenuHtml());
        return []; // $menu->GetMenuHtml();
    }

	public function getSections($IBLOCK_ID, $DEPTH_LEVEL=0)
	{
		global $APPLICATION;
		
		$arSections=array();
		
    	if (\Bitrix\Main\Loader::includeModule('iblock')) {
            $arFilter=['IBLOCK_ID'=>$IBLOCK_ID, 'ACTIVE'=>'Y', 'GLOBAL_ACTIVE'=>'Y'];
            if($DEPTH_LEVEL > 0) {
                $arFilter['>=DEPTH_LEVEL']=$DEPTH_LEVEL;
            }
        	$rsSection = \CIBlockSection::GetTreeList($arFilter,
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

