<?
namespace kontur\import;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

AddEventHandler('main', 'OnBuildGlobalMenu', ['\kontur\import\AdminIBlock', 'addAdminMenuItem']);

class AdminIBlock
{
    public static function getAdminMenuItems()
    {
		$items = [
			[
				'text' => "Очистить кэш",
                'url' => 'admin_clear_all_domains_cache.php?lang=ru',
                'module_id' => 'iblock',
                'more_url' => ['admin_clear_all_domains_cache.php']
            ]
		];
		
        return $items;
    }
    public static function addAdminMenuItem(&$arGlobalMenu, &$arModuleMenu) 
    {
		foreach($arModuleMenu as $idxMenu=>$arMenu) { if(isset($_GET['dbg'])) var_dump($arMenu);
            if(($arMenu['parent_menu'] == 'global_menu_content')) {
                foreach($arMenu['items'] as $idxItem=>$arItem) {
                    if($arItem['items_id'] == 'iblock_redirect') {
                        foreach(static::getAdminMenuItems() as $arCustom) {
							$qs=[];
							if(!empty($_REQUEST['lang'])) $qs['lang']=$_REQUEST['lang'];
							if(!empty($_REQUEST['PROFILE_ID'])) $qs['PROFILE_ID']=$_REQUEST['PROFILE_ID'];
							$arCustom['_active'] = ($_SERVER['SCRIPT_NAME'].'?'.http_build_query($qs) == '/bitrix/admin/'.$arCustom['url']);
                            $arModuleMenu[$idxMenu]['items'][$idxItem]['items'][] = $arCustom;
                        }
                        break;
                    }
                }
            }
        }
    }

}

