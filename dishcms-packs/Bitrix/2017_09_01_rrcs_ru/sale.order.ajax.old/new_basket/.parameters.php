<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arTemplateParameters = array(
	"ALLOW_NEW_PROFILE" => array(
		"NAME"=>GetMessage("T_ALLOW_NEW_PROFILE"),
		"TYPE" => "CHECKBOX",
		"DEFAULT"=>"Y",
		"PARENT" => "BASE",
	),
	"SHOW_PAYMENT_SERVICES_NAMES" => array(
		"NAME" => GetMessage("T_PAYMENT_SERVICES_NAMES"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" =>"Y",
		"PARENT" => "BASE",
	),
	"SHOW_STORES_IMAGES" => array(
		"NAME" => GetMessage("T_SHOW_STORES_IMAGES"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" =>"N",
		"PARENT" => "BASE",
	),
	/*"LOCATIONS_DEFAULT_CITY_ID" => array(
		"NAME" => "Идентификатор города по умолчанию",
		"TYPE" => "STRING",
		"DEFAULT" =>"2615",
		"PARENT" => "BASE",
	),*/
	"LOCATIONS_JS_CONTROL_GLOBAL_ID"=>array(
		"NAME" => "Системный идентификатор местоположения",
		"TYPE" => "STRING",
		"DEFAULT" =>"",
		"PARENT" => "BASE",
	),
	"LOCATIONS_DEFAULT_ID" => array(
		"NAME" => "Идентификатор местоположения по умолчанию",
		"TYPE" => "STRING",
		"DEFAULT" =>"",
		"PARENT" => "BASE",
	),
	"LOCATIONS_DEFAULT_CODE" => array(
		"NAME" => "Символьный код местоположения по умолчанию",
		"TYPE" => "STRING",
		"DEFAULT" =>"",
		"PARENT" => "BASE",
	),
	"DELIVERY_SELFPICKUP_ID" => array(
		"NAME" => "Идентификатор доставки САМОВЫВОЗ",
		"TYPE" => "STRING",
		"DEFAULT" =>"",
		"PARENT" => "BASE",
	),
	"DELIVERY_OUTNSK_ID" => array(
		"NAME" => "Идентификатор доставки за пределами Новосибирска",
		"TYPE" => "STRING",
		"DEFAULT" =>"",
		"PARENT" => "BASE",
	),
);
