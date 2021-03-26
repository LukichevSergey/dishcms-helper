<? 
/**
 * @link https://dev.1c-bitrix.ru/learning/course/?COURSE_ID=43&LESSON_ID=2132
 */
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentParameters = array(
    "GROUPS" => array(),
    "PARAMETERS" => array(
        "MYPARAM" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("PARAMETER_MYPARAM_NAME"),
            "TYPE" => "STRING",
            "MULTIPLE" => "N",
            "DEFAULT" => "",
            "REFRESH" => "Y",
        ),
    ),
);
?>