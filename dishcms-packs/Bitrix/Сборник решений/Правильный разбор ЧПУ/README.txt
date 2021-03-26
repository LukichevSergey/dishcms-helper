1) Добавить перед кодом компонента на странице подключение "kontur_catalog_sef_urlrewrite.php"
include(__DIR__ . DIRECTORY_SEPARATOR . 'kontur_catalog_sef_urlrewrite.php');

Добавить в result_modifier.php компонента bitrix:catalog строки:
if(!empty($_REQUEST['urlrewrite_element_id'])) {
	$arResult['VARIABLES']['ELEMENT_ID']=$_REQUEST['urlrewrite_element_id'];
}
* в случае, когда параметр компонента bitrix:catalog.element 
Array(..., 'ELEMENT_ID' => $arResult['VARIABLES']['ELEMENT_ID'], ...)
