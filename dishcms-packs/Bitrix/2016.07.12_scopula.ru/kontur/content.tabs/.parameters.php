<? 
/**
 * @link https://dev.1c-bitrix.ru/learning/course/?COURSE_ID=43&LESSON_ID=2132
 */
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$iGroupSort=9000;
$arComponentParameters = array(
    "GROUPS" => array(
        "TABS_SETTINGS" => array(
            "NAME" => "Настройки вкладок",
            "SORT" => $iGroupSort
        )
    )
);

$arComponentParameters['PARAMETERS']['TABS_COUNT']=array(
    'PARENT'=>'TABS_SETTINGS',
    'NAME'=>'Количество вкладок',
    'TYPE'=>'STRING',
    'DEFAULT'=>0,
    'REFRESH'=>'Y'
);

if(!empty($arCurrentValues['TABS_COUNT'])) {
	for($i=1 ; $i<=(int)$arCurrentValues['TABS_COUNT']; $i++) {
		$arComponentParameters['GROUPS']['TABS_ITEM_'.$i]=array(
            'NAME'=>'Вкладка #'.$i,
            'SORT'=>$iGroupSort + $i*100
        );
        $arComponentParameters['PARAMETERS']['TABS_ITEM_NAME_'.$i]=array(
            'PARENT'=>'TABS_ITEM_'.$i,
            'NAME'=>'Наименование',
            'TYPE'=>'STRING',
        );
        $arComponentParameters['PARAMETERS']['TABS_ITEM_SORT_'.$i]=array(
            'PARENT'=>'TABS_ITEM_'.$i,
            'NAME'=>'Сортировка',
            'TYPE'=>'STRING',
            'DEFAULT'=>500
        );
        $arComponentParameters['PARAMETERS']['TABS_ITEM_VISIBLE_'.$i]=array(
            'PARENT'=>'TABS_ITEM_'.$i,
            'NAME'=>'Отображать',
            'TYPE'=>'CHECKBOX',
            'DEFAULT'=>'Y'
        );
        $arComponentParameters['PARAMETERS']['TABS_ITEM_FILE_'.$i]=array(
            'PARENT'=>'TABS_ITEM_'.$i,
            'NAME'=>'Файл подключения. В пути могут быть использованы шаблоны: #SITE_DIR# - путь от корня сайта; #SITE_TEMPLATE_PATH# - путь от текущего шаблона сайта',
            'TYPE'=>'STRING',
        );
	}
}
?>
