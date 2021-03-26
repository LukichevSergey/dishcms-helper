<? 
/**
 * @link https://dev.1c-bitrix.ru/learning/course/?COURSE_ID=43&LESSON_ID=2132
 */
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

CModule::IncludeModule("iblock");

$arComponentParameters = array(
    "GROUPS" => array(),
    "PARAMETERS" => array(
        "IBLOCK_TYPE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("KONTUR_CORE_IBLOCK_DESC_LIST_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => \kontur\IBlock::GetIBlockTypes(),
			"DEFAULT" => "",
			"REFRESH" => "Y",
		),
		"IBLOCK_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("KONTUR_CORE_IBLOCK_DESC_LIST_ID"),
			"TYPE" => "LIST",
			"VALUES" => \kontur\IBlock::GetIBlockNames($_REQUEST["site"], $arCurrentValues),
			"DEFAULT" => '',
			"ADDITIONAL_VALUES" => "Y",
			"REFRESH" => "Y",
		)
    ),
);
?>