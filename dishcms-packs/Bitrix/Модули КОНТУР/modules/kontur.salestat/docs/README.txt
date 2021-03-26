Подмена пункта меню 
1) Переименовать файл admin/menu.php в admin/_menu.php
2) добавить в init.php

AddEventHandler('main', 'OnBuildGlobalMenu', 'looklikeOnBuildGlobalMenu');
if(!function_exists('looklikeOnBuildGlobalMenu')) {
	function looklikeOnBuildGlobalMenu(&$arGlobalMenu, &$arModuleMenu) {
		$found=false;
		foreach($arModuleMenu as $idxMenu=>$arMenu) {
        	if(($arMenu['parent_menu'] == 'global_menu_store') && ($arMenu['items_id'] == 'menu_sale_stat')) {
                foreach($arMenu['items'] as $idxItem=>$arItem) {
        	        if(strpos($arItem['url'], 'sale_stat_products.php') !== false) {
                    	$arModuleMenu[$idxMenu]['items'][$idxItem]['url']=str_replace('sale_stat_products.php', 'sale_stat_products_custom.php', $arItem['url']);
                    	$found=true;
                    	break;
                    }
                }
    		}
    		if($found) { break; }
    	}
	}
}