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

		$profiles=[
			'Барнаул'=>1,
			'Абакан'=>2,
			'Братск'=>3,
			'Чита'=>4,
			'Хабаровск'=>5,
			'Иркутск'=>6,
			'Кемерово'=>7,
			'Красноярск'=>8,
			'Магадан'=>9,
			'Новокузнецк'=>10,
			'Петропавловск-Камчатский'=>11,
			'Томск'=>12,
			'Тува'=>13,
			'Южно-Сахалинск'=>14,
			'Улан-Удэ'=>15,
			'Владивосток'=>16,
			'Якутск'=>17
		];
		foreach($profiles as $city=>$id) {
			$items[] = [
				'text' => "Импорт ({$city})",
                'url' => 'esol_import_excel.php?lang=ru&PROFILE_ID='.$id,
                'module_id' => 'iblock',
                'more_url' => ['esol_import_excel.php']
            ];
		}
        return $items;
    }
    public static function addAdminMenuItem(&$arGlobalMenu, &$arModuleMenu) 
    {
		foreach($arModuleMenu as $idxMenu=>$arMenu) {
            if(($arMenu['parent_menu'] == 'global_menu_content') && ($arMenu['section'] == 'esol_importexportexcel_import') && ($arMenu['module_id'] == 'esol.importexportexcel')) {
                foreach($arMenu['items'] as $idxItem=>$arItem) {
                    if($arItem['items_id'] == 'menu_esol_importexportexcel') {
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

