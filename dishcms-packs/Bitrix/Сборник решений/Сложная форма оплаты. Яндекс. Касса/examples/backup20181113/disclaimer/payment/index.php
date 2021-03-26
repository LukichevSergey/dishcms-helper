<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Оплата заявления об отказе от взаимодействия");
?><?$APPLICATION->IncludeComponent("kiora:yandex.form.payment", "ykassa", Array(
	"ITEM_NAME" => "Заявление",	// Содержание заказа
		"MODAL_FORM" => "N",	// Модальная форма
		"PAYMENT_TYPE" => "",	// Способ оплаты
		"SHOP_ID" => "509505",	// ShopID Яндекс Кассы
		"SUM" => "0",	// Сумма заказа
		"SUM_PRINT" => "0 .",	// Сумма заказа для вывода на экран
	),
	false
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>