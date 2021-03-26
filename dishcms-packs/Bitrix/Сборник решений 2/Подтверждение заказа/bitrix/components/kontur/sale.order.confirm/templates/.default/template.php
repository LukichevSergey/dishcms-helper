<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$APPLICATION->IncludeComponent("bitrix:sale.basket.basket", "cart_order_confirm", Array(
	"COUNT_DISCOUNT_4_ALL_QUANTITY" => "N",	// Рассчитывать скидку для каждой позиции (на все количество товара)
		"COLUMNS_LIST" => array(	// Выводимые колонки
			0 => "NAME",
			1 => "DISCOUNT",
			2 => "PROPS",
			3 => "DELETE",
			4 => "DELAY",
			5 => "PRICE",
			6 => "QUANTITY",
			7 => "SUM",
		),
		"AJAX_MODE" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N",
		"PATH_TO_ORDER" => "/personal/order/make/",	// Страница оформления заказа
		"HIDE_COUPON" => "Y",	// Спрятать поле ввода купона
		"QUANTITY_FLOAT" => "N",	// Использовать дробное значение количества
		"PRICE_VAT_SHOW_VALUE" => "N",	// Отображать значение НДС
		"SET_TITLE" => "Y",	// Устанавливать заголовок страницы
		"AJAX_OPTION_ADDITIONAL" => "",
		"OFFERS_PROPS" => "",	// Свойства, влияющие на пересчет корзины
		"USE_PREPAYMENT" => "N",	// Использовать предавторизацию для оформления заказа (PayPal Express Checkout)
		"ACTION_VARIABLE" => "action",	// Название переменной действия
		"BASKET_AJAX_URL" => "/ajax/basket.php",	// Ajax ссылка скрипта корзины
	),
	false
);?>

