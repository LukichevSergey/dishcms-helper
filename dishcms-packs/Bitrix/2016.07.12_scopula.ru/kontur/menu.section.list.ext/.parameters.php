<? 
/**
 * @link https://dev.1c-bitrix.ru/learning/course/?COURSE_ID=43&LESSON_ID=2132
 */
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();


$arComponentParameters = array(
    "GROUPS" => array(
        "MENU_ITEMS" => array(
            "NAME" => "Пункты меню",
            "SORT" => 700
        )
    )
);

$arComponentParameters['PARAMETERS']['MENU_ITEMS_COUNT']=array(
    'PARENT'=>'MENU_ITEMS',
    'NAME'=>'Количество пунктов меню',
    'TYPE'=>'STRING',
    'DEFAULT'=>0,
    'REFRESH'=>'Y'
);
$arComponentParameters['PARAMETERS']['MENU_ITEMS_IBLOCK_ID']=array(
    'PARENT'=>'MENU_ITEMS',
    "NAME"=>"Идентификатор инфоблока каталога",
    "TYPE"=>"STRING",
    "REFRESH"=>"Y"
);
$arComponentParameters['PARAMETERS']['MENU_ITEMS_USE_GLOBALY']=array(
    'PARENT'=>'MENU_ITEMS',
    "NAME"=>"Использовать глобально",
    "TYPE"=>"CHECKBOX",
    "REFRESH"=>"N"
);

if(!empty($arCurrentValues['MENU_ITEMS_IBLOCK_ID'])
    && !empty($arCurrentValues['MENU_ITEMS_COUNT'])
    && is_numeric($arCurrentValues['MENU_ITEMS_IBLOCK_ID'])
    && is_numeric($arCurrentValues['MENU_ITEMS_COUNT']))
{
    $arSections=array();
    if (\Bitrix\Main\Loader::includeModule('iblock')) {
        $rsSection = CIBlockSection::GetTreeList(
            array('IBLOCK_ID'=>$arCurrentValues['MENU_ITEMS_IBLOCK_ID'], 'ACTIVE'=>'Y'),
            array('ID', 'NAME', 'DEPTH_LEVEL', 'LEFT_MARGIN')
        );
        while($arSection = $rsSection->Fetch()) {
            $arSections[ $arSection['ID'] ] = str_repeat(' - ', $arSection['DEPTH_LEVEL']-1) . $arSection['NAME'];
        }
    }
    foreach(range(1,$arCurrentValues['MENU_ITEMS_COUNT']) as $i) {
        $arComponentParameters['GROUPS']['MENU_ITEMS_'.$i]=array(
            'NAME'=>'Пункт меню #'.$i
        );
        $arComponentParameters['PARAMETERS']['MENU_ITEMS_NAME_'.$i]=array(
            'PARENT'=>'MENU_ITEMS_'.$i,
            'NAME'=>'Наименование',
            'TYPE'=>'STRING'
        );
        $arComponentParameters['PARAMETERS']['MENU_ITEMS_SORT_'.$i]=array(
            'PARENT'=>'MENU_ITEMS_'.$i,
            'NAME'=>'Сортировка',
            'TYPE'=>'STRING',
            'DEFAULT'=>'500'
        );
        $arComponentParameters['PARAMETERS']['MENU_ITEMS_LINK_'.$i]=array(
            'PARENT'=>'MENU_ITEMS_'.$i,
            'NAME'=>'Ссылка',
            'TYPE'=>'STRING'
        );
		$arComponentParameters['PARAMETERS']['MENU_ITEMS_IMAGE_'.$i]=array(
            'PARENT'=>'MENU_ITEMS_'.$i,
            'NAME'=>'Изображение',
            'TYPE'=>'FILE',
            "FD_TARGET" => "F",
		    "FD_EXT" => 'jpg,jpeg,gif,png',
		    "FD_UPLOAD" => true,
		    "FD_USE_MEDIALIB" => true
        );
        $arComponentParameters['PARAMETERS']['MENU_ITEMS_SECTIONS_'.$i]=array(
            'PARENT'=>'MENU_ITEMS_'.$i,
            'NAME'=>'Разделы, как подпункты меню',
            'TYPE'=>'LIST',
            "VALUES"=>$arSections,
            "MULTIPLE"=>"Y",
            "SIZE"=>10
        );
    }
}
?>
