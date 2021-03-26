<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentParameters = array(
	"GROUPS" => array(
		"PHONES" => array(
			"NAME" => GetMessage('KONTUR_PHONES_GROUP_PHONES_NAME'),
			"SORT" => 500
		)
	),
	"PARAMETERS" => array(
		"PHONE_NUMBER_1" => Array(
			"NAME" => GetMessage("KONTUR_PHONES_PHONE_NUMBER_1"),
			"TYPE" => "STRING",
			"PARENT" => "PHONES"
		),
		"PHONE_NUMBER_1_TEXT" => Array(
            "NAME" => GetMessage("KONTUR_PHONES_PHONE_NUMBER_1_TEXT"),
            "TYPE" => "STRING",
            "PARENT" => "PHONES"
        ),
 		"PHONE_NUMBER_2" => Array(
            "NAME" => GetMessage("KONTUR_PHONES_PHONE_NUMBER_2"),
            "TYPE" => "STRING",
            "PARENT" => "PHONES"
        ),
        "PHONE_NUMBER_2_TEXT" => Array(
            "NAME" => GetMessage("KONTUR_PHONES_PHONE_NUMBER_2_TEXT"),
            "TYPE" => "STRING",
            "PARENT" => "PHONES"
        )
	)
);
?>
