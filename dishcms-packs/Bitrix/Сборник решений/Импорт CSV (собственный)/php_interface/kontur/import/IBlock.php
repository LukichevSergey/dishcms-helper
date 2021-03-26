<?php
namespace kontur\import;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

AddEventHandler('main', 'OnBuildGlobalMenu', ['\kontur\import\IBlock', 'addAdminMenuItem']);

class IBlock
{
	public static function getAdminMenuItems()
	{
		return [
			// скопировать providers/iblock_data_import_filials.php в /bitrix/admin
			[
	            'text' => 'Филиалы',
                'url' => 'iblock_data_import_filials.php?lang=ru',
                'module_id' => 'iblock',
                'more_url' => ['iblock_data_import_filials.php']
            ]
		];
	}
    public static function addAdminMenuItem(&$arGlobalMenu, &$arModuleMenu) 
    {
        foreach($arModuleMenu as $idxMenu=>$arMenu) {
            if(($arMenu['parent_menu'] == 'global_menu_content') && ($arMenu['section'] == 'iblock') && ($arMenu['module_id'] == 'iblock')) {
                foreach($arMenu['items'] as $idxItem=>$arItem) {
                    if($arItem['items_id'] == 'iblock_import') {
						foreach(static::getAdminMenuItems() as $arCustom) {
	                        $arModuleMenu[$idxMenu]['items'][$idxItem]['items'][] = $arCustom;
						}
                        break;
                    }
                }
            }
        }
    }

}
