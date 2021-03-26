<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<? $hasErrors=false; ?>
<h5>Тип плательщика</h5>
<div><?= $arResult['ORDER']['PERSON_TYPE']['NAME']; ?></div>

<h4>ИНФОРМАЦИЯ ДЛЯ ОПЛАТЫ И ДОСТАВКИ ЗАКАЗА</h4>
<? if(!empty($arResult['ORDER']['PROPERTIES'])):?>
	<h5>ИНФОРМАЦИЯ О ПОКУПАТЕЛЕ</h5>
	<? foreach($arResult['ORDER']['PROPERTIES'] as $arProp): //var_dump($arProp); ?>
		<? 
		if($arProp['IS_LOCATION'] == 'Y') {
			if($arLocation=CSaleLocation::GetByID($arProp['VALUE'])) {
				$value=$arLocation['COUNTRY_NAME_LANG'].', '.$arLocation['CITY_NAME'];
			}
			else $value='';
		}
		else $value=$arProp['VALUE'];
		?>
		<div<? if(($arProp['REQUIED'] == 'Y') && empty($value)) { echo ' class="error"'; $hasErrors=true; };?>><span><?= $arProp['NAME'] ?>:</span> <?= empty($value) ? '<i>не указано</i>' : $value; ?></div>
	<? endforeach; ?>
<? endif; //BUYER_STORE ?>

<? if(!empty($arResult['ORDER']['DELIVERY']['NAME'])): ?>
<h5>СЛУЖБА ДОСТАВКИ</h5>
	<div><?= $arResult['ORDER']['DELIVERY']['NAME']; ?></div>
	<? if(!empty($arResult['ORDER']['BUYER_STORE'])): ?>
		<div><?=$arResult['ORDER']['BUYER_STORE']['TITLE']?></div>
	<? endif; ?>
<? endif; ?>

<h5>ПЛАТЕЖНАЯ СИСТЕМА</h5>
<div><?= $arResult['ORDER']['PAY_SYSTEM']['NAME']; ?></div>

<h5>КОММЕНТАРИИ К ЗАКАЗУ</h5>
<? if(!empty($arResult['ORDER']['ORDER_DESCRIPTION'])): ?>
	<div><?= $arResult['ORDER']['ORDER_DESCRIPTION']; ?></div>
<? else: ?>
	<div class="empty">Не указан</div>
<? endif; ?>

<h4>СОСТАВ ЗАКАЗА</h4>
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
		"DELIVERY_PRICE" => (empty($arResult['ORDER']['DELIVERY']['PRICE']) ? 0 : $arResult['ORDER']['DELIVERY']['PRICE'])
	),
	false
);?>
<? if($hasErrors): ?><div data-id="kontur_sale_order_confirm_has_errors" style="display:none !important"></div><? endif; ?>
