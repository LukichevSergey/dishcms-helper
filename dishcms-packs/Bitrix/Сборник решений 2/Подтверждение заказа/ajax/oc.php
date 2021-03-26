<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>
<? $APPLICATION->IncludeComponent("kontur:sale.order.confirm", "main", Array(
    "COMPONENT_TEMPLATE" => ".default",
        "BTN_ORDER_URL" => "/personal/cart/make",   // Ссылка кнопки подтверждения
        "BTN_ORDER_LABEL" => "Оформить",    // Подпись кнопки подтверждения
    ),
    false
); ?>
