<? 
/**
 * @link https://dev.1c-bitrix.ru/learning/course/?COURSE_ID=43&LESSON_ID=2132
 */
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentParameters = array(
    "GROUPS" => array(
    	'KONTUR_SALE_ORDER_CONFIRM'=>array(
    		'NAME'=>GetMessage('KONTUR_SALE_OC_GROUP_NAME')
    	)
    ),
    "PARAMETERS" => array(
        "BTN_ORDER_URL" => array(
            "PARENT" => "KONTUR_SALE_ORDER_CONFIRM",
            "NAME" => GetMessage("KONTUR_SALE_OC_BTN_ORDER_URL"),
            "TYPE" => "STRING",
            "DEFAULT" => GetMessage("KONTUR_SALE_OC_BTN_ORDER_URL_DEFAULT")
        ),
        "BTN_ORDER_LABEL" => array(
            "PARENT" => "KONTUR_SALE_ORDER_CONFIRM",
            "NAME" => GetMessage("KONTUR_SALE_OC_BTN_ORDER_LABEL"),
            "TYPE" => "STRING",
            "DEFAULT" => GetMessage("KONTUR_SALE_OC_BTN_ORDER_LABEL_DEFAULT")
        ),
    ),
);
?>