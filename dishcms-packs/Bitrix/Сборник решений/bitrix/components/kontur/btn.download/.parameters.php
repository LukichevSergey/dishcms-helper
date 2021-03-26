<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentParameters = array(
    "PARAMETERS" => array(
        "LABEL" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("KONTUR_BTN_DOWNLOAD_PARAM_LABEL"),
            "TYPE" => "STRING",
            "DEFAULT" => GetMessage("KONTUR_BTN_DOWNLOAD_LABEL"),
        ),
        "FILE" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("KONTUR_BTN_DOWNLOAD_PARAM_FILE"),
            "TYPE" => "FILE",
            "FD_TARGET" => "F",
		    // "FD_EXT" => 'pdf,doc,txt,ppt',
		    "FD_UPLOAD" => true,
		    "FD_USE_MEDIALIB" => true
        ),
        "FILENAME" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("KONTUR_BTN_DOWNLOAD_PARAM_FILENAME"),
            "TYPE" => "STRING",
            "DEFAULT" => "",
        ),
        "ZIP" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("KONTUR_BTN_DOWNLOAD_PARAM_ZIP"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "N"
        ),
        "UNLINK_ZIP" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("KONTUR_BTN_DOWNLOAD_PARAM_UNLINK_ZIP"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y"
        ),
    ),
);
?>
