<?php
namespace Kontur;

class Custom
{
	/**
     * Предобработка отображения глобального меню раздела администрирования.
     * @link https://idea.1c-bitrix.ru/hide-inactive-infobloki-in-the-left-tree/
     * 1) скрывает неактивные инфоблоки из раздела "Контент". 
     */
	public static function AdminMenu(&$arGlobalMenu, &$arModuleMenu) 
	{
   		\CModule::IncludeModule("iblock");
   		$dbIBlocks = \CIBlock::GetList(
        	array(),
         	array("ACTIVE" => "N"),
         	false
      	);

   		while($arIBlock = $dbIBlocks->GetNext()) {
	    	$inactive[] = 'menu_iblock_/'.$arIBlock['IBLOCK_TYPE_ID'].'/'.$arIBlock['ID'];
   		}

   		foreach($arModuleMenu as $k => $v) {
        	if($v['parent_menu']=='global_menu_content' && substr($v['items_id'],0,13)=='menu_iblock_/') {
            	foreach($v['items'] as $key => $val) {
		            if(in_array($val['items_id'],$inactive)) {
        		    	unset($arModuleMenu[$k]['items'][$key]);
		       		}
				}
		    }
    	}
	}
}
