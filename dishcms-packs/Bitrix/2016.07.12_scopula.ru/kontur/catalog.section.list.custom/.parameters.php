<? 
/**
 * @link https://dev.1c-bitrix.ru/learning/course/?COURSE_ID=43&LESSON_ID=2132
 */
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arSections=array();
if (\Bitrix\Main\Loader::includeModule('iblock')) {
        $rsSection = CIBlockSection::GetTreeList(
            array('IBLOCK_ID'=>$arCurrentValues['IBLOCK_ID'], 'ACTIVE'=>'Y'),
            array('ID', 'NAME', 'DEPTH_LEVEL', 'LEFT_MARGIN')
        );
        while($arSection = $rsSection->Fetch()) {
            $arSections[ $arSection['ID'] ] = str_repeat(' - ', $arSection['DEPTH_LEVEL']-1) . $arSection['NAME'];
     	}
}

$arComponentParameters = array(
    "PARAMETERS" => array(
        "IBLOCK_ID" => array(
            "PARENT" => "BASE",
            "NAME" => 'ID информационного блока',
            "TYPE" => "STRING",
            "REFRESH" => "Y"
        ),
        "SECTIONS"=>array(
            'NAME'=>'Разделы',
            'TYPE'=>'LIST',
            "VALUES"=>$arSections,
            "MULTIPLE"=>"Y",
            "SIZE"=>10
    	),
	)
);
?>