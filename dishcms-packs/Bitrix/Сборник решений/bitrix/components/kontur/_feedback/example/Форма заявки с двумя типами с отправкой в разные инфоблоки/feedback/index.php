<?
$IS_AJAX = false;
if( isset($_SERVER['HTTP_X_REQUESTED_WITH']) || $_REQUEST['AJAX_CALL']=='Y' ) {
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
	$IS_AJAX = true;
} else {
	require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
	$APPLICATION->SetTitle("Задать вопрос / подать заявку");
} 
?><?$APPLICATION->IncludeComponent(
	"kontur:feedback.form", 
	"2types", 
	array(
		"COMPONENT_TEMPLATE" => "2types",
		"EVENT_TYPE" => "KONTUR_FEEDBACK_FORM",
		"EVENT_ID" => "46",
		"IBLOCK_TYPE" => "feedback",
		"IBLOCK_ID" => "18",
		"KFF_FIELD_CODE" => array(
			0 => "NAME",
			1 => "PREVIEW_TEXT",
			2 => "",
		),
		"KFF_PROPERTY_CODE" => array(
			0 => "PHONE",
			1 => "EMAIL",
			2 => "TYPE",
			3 => "",
		),
		"KFF_FIELD_CODE_NAME" => "NAME",
		"KFF_FIELD_LABEL_NAME" => "ФИО",
		"KFF_FIELD_TYPE_NAME" => "S",
		"KFF_FIELD_SORT_NAME" => "100",
		"KFF_FIELD_CODE_PREVIEW_TEXT" => "MESSAGE",
		"KFF_FIELD_LABEL_PREVIEW_TEXT" => "Сообщение",
		"KFF_FIELD_TYPE_PREVIEW_TEXT" => "T",
		"KFF_FIELD_SORT_PREVIEW_TEXT" => "400",
		"KFF_PROPERTY_CODE_PHONE" => "PHONE",
		"KFF_PROPERTY_LABEL_PHONE" => "Телефон",
		"KFF_PROPERTY_TYPE_PHONE" => "PH",
		"KFF_PROPERTY_SORT_PHONE" => "200",
		"KFF_PROPERTY_CODE_EMAIL" => "EMAIL",
		"KFF_PROPERTY_LABEL_EMAIL" => "E-Mail",
		"KFF_PROPERTY_TYPE_EMAIL" => "E",
		"KFF_PROPERTY_SORT_EMAIL" => "300",
		"KFF_PROPERTY_CODE_TYPE" => "TYPE",
		"KFF_PROPERTY_LABEL_TYPE" => "Тип заявки",
		"KFF_PROPERTY_TYPE_TYPE" => "LR",
		"KFF_PROPERTY_SORT_TYPE" => "500",
		"KFF_FORM_ID" => "KFF",
		"KFF_FIELD_REQUIRE_NAME" => "Y",
		"KFF_FIELD_REQUIRE_PREVIEW_TEXT" => "N",
		"KFF_PROPERTY_REQUIRE_PHONE" => "N",
		"KFF_PROPERTY_REQUIRE_EMAIL" => "Y",
		"KFF_PROPERTY_REQUIRE_TYPE" => "Y",
		"IBLOCK_TYPE_2" => "feedback",
		"IBLOCK_ID_2" => "19",
		"KFF_FORM_HASH" => "0ecb0293f5c70f112cfa7d2e606a84eb",
		"KFF_FIELD_REQUIRE_ERROR_NAME" => "",
		"KFF_FIELD_REQUIRE_ERROR_PREVIEW_TEXT" => "",
		"KFF_PROPERTY_REQUIRE_ERROR_PHONE" => "",
		"KFF_PROPERTY_REQUIRE_ERROR_EMAIL" => "",
		"KFF_PROPERTY_REQUIRE_ERROR_TYPE" => "Выберите тип заявки",
		"EVENT_TYPE_2" => "KONTUR_FEEDBACK_FORM",
		"EVENT_ID_2" => "47"
	),
	false
);?><?if(!$IS_AJAX):?>
<?require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');?>
<?endif;?>
