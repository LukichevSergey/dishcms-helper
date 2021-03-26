<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentParameters = array(
	"GROUPS" => array(
		"SOCIAL_LINKS" => array(
			"NAME" => GetMessage('KONTUR_SL_GROUP_SOCIAL_LINKS_NAME'),
			"SORT" => 500
		)
	),
	"PARAMETERS" => array(
		"SOC_TITLE" => Array(
			"NAME" => GetMessage("KONTUR_SL_SOC_TITLE_NAME"),
			"TYPE" => "STRING",
			"PARENT" => "SOCIAL_LINKS"
		),

		"SOC_VK_URL" => Array(
			"NAME"=>GetMessage("KONTUR_SL_SOC_VK_URL_NAME"),
			"TYPE" => "STRING",
			"PARENT" => "SOCIAL_LINKS",
		),

		"SOC_YOUTUBE_URL" => Array(
			"NAME"=>GetMessage("KONTUR_SL_SOC_YOUTUBE_URL_NAME"),
			"TYPE" => "STRING",
			"PARENT" => "SOCIAL_LINKS",
		),

		"SOC_IM_URL" => Array(
			"NAME"=>GetMessage("KONTUR_SL_SOC_INSTAGRAM_URL_NAME"),
			"TYPE" => "STRING",
			"PARENT" => "SOCIAL_LINKS",
		),

		"SOC_FB_URL" => Array(
			"NAME"=>GetMessage("KONTUR_SL_SOC_FB_URL_NAME"),
			"TYPE" => "STRING",
			"PARENT" => "SOCIAL_LINKS",
		),

		"SOC_LJ_URL" => Array(
            "NAME"=>GetMessage("KONTUR_SL_SOC_LJ_URL_NAME"),
            "TYPE" => "STRING",
            "PARENT" => "SOCIAL_LINKS",
        ),

//		"SOC_OK_URL" => Array(
//            "NAME"=>GetMessage("KONTUR_SL_SOC_OK_URL_NAME"),
//            "TYPE" => "STRING",
//            "PARENT" => "SOCIAL_LINKS",
//        ),

		'SOC_FLAMP_URL' => array(
		    'PARENT' => 'SOCIAL_LINKS',
		    'NAME' => GetMessage('KONTUR_SL_SOC_FLAMP_URL_NAME'),
		    'TYPE' => 'STRING'
		)
	)
);
?>
