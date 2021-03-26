1) Добавить инфоблок "Города"
Свойства (код свойства может быть произвольный):
* Является регионом по умолчанию / Список (значение Y) / IS_DEFAULT
* Местоположение / Привязка к местоположению с автозаполнением 2.0 / LOCATION
(требуется установить бесплатное решение "Свойство инфоблока - привязка к местоположению" https://marketplace.1c-bitrix.ru/solutions/webfarrock.iblockproplocation/)
* Адрес / Строка / ADDRESS
(можно использовать как дополнительное описание)
* Карта / Привязка к Яндекс.Карте / MAP

2) Добавить подключение библиотек в init.php
require_once dirname(__FILE__) . '/kontur/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/local/components/kontur/regions.citychange/lib/helper.php';
KRCCHelper::i()->init(['IBLOCK_ID'=>59, 'COOKIE_KEY'=>'krccity', 'PROPERTY_IS_DEFAULT_CODE'=>'IS_DEFAULT']);

2) Разместить компонент выбора города (пример)
<?$APPLICATION->IncludeComponent(
	"kontur:regions.citychange", 
	".default", 
	array(
		"COMPONENT_TEMPLATE" => ".default",
		"IBLOCK_TYPE" => "handbooks",
		"IBLOCK_ID" => "59",
		"LOCATION_PROPERTY_CODE" => "LOCATION",
		"ADDRESS_PROPERTY_CODE" => "ADDRESS",
		"MAP_PROPERTY_CODE" => "MAP",
		"YMAP_API_KEY" => "",
		"IS_DEFAULT_PROPERTY_CODE" => "IS_DEFAULT",
		"COOKIE_KEY" => "krccity",
		"DISABLE_BX_GEOIP" => "N",
		"DISABLE_YANDEX_GEOIP" => "N"
	),
	false
);?>

3) Для отображения уникальных блоков для регионов нужно завести дополнительные свойства для инфоблока
напр. 
Телефон / Строка / PHONE

Код вставки на сайте (второй параметр, значение по умолчанию)
У метода есть еще третий параметр (boolean) "использовать пустое значение".
Другими словами, если передано "true", и значение для региона не задано, то будет отображена пустая строка.
А при значении "false", если значение для региона не задано, будет отображено значение по умолчанию.
<?=KRCCHelper::i()->get('PHONE', '8 (383) 255-33-33');?>

В качестве "значения по умолчанию" может быть передана callback функция. 
Например для отображения по умолчанию какого-либо компонента:

<?=KRCCHelper::i()->get('FOOTER_COPYRIGHT', function() {
	global $APPLICATION; $APPLICATION->IncludeFile(
		SITE_DIR."include/footer/copy/copyright.php", 
		Array(), 
		Array("MODE"=>"html", "NAME"=>GetMessage("COPYRIGHT"))
	);
});?>
