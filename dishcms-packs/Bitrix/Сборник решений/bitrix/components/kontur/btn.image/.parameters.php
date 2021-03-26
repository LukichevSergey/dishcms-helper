<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentParameters = array(
    "PARAMETERS" => array(
        "LINK" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("KONTUR_BTN_IMAGE_PARAM_LINK"),
            "TYPE" => "STRING"
        ),
        "IMAGE_SRC" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("KONTUR_BTN_IMAGE_PARAM_IMAGE_SRC"),
            "TYPE" => "FILE",
            "FD_TARGET" => "F",
		    "FD_EXT" => 'jpg,jpeg,png,gif',
		    "FD_UPLOAD" => true,
		    "FD_USE_MEDIALIB" => true
        ),
        "IMAGE_ALT" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("KONTUR_BTN_IMAGE_PARAM_IMAGE_ALT"),
            "TYPE" => "STRING"
        ),
    ),
);
?>