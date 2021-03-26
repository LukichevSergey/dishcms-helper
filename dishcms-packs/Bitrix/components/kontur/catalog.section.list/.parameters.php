<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentParameters = array(
	"GROUPS" => array(
		"SECTION_LIST" => array(
			"NAME" => GetMessage('KONTUR_CSL_SECTION_LIST_NAME'),
			"SORT" => 500
		)
	),
	"PARAMETERS" => array(
		"LIST_NAME"=> Array(
            "NAME" => GetMessage("KONTUR_CSL_LIST_NAME_NAME"),
            "TYPE" => "STRING",
            "PARENT" => "SECTION_LIST"
        ),
	)
);
?>
