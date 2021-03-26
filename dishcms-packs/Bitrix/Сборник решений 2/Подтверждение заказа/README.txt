Настройка:
1) В шаблон bitrix:sale.order.ajax\custom\template.php добавить код:

----------------------------------------------------------------------------------

<div style="display:none !important"><? $APPLICATION->IncludeComponent("kontur:sale.order.confirm", "main", Array(
    "COMPONENT_TEMPLATE" => ".default",
        "BTN_ORDER_URL" => "/personal/cart/make",   // Ссылка кнопки подтверждения
        "BTN_ORDER_LABEL" => "Оформить",    // Подпись кнопки подтверждения
    ),
    false
); ?></div>
<script>$(document).ready(function() {
	function get_data() {
		var data={};
		$("#ORDER_FORM").find("input:not(:checkbox):not(:radio),:checkbox:checked,:radio:checked,textarea,select").each(function() {
            data[($(this).attr("name") ? $(this).attr("name") : 'id'+$(this).attr("id"))]=$(this).val();
        });
		return data;
	}
	$(document).on("click", "#ORDER_CONFIRM_BUTTON", function(e) {
		e.preventDefault();
		$.post('/ajax/oc.php', get_data(), function(response) {
			kontur_bx_popup({
				title:"Подтверждение заказа", 
				text: response, 
				btnClose:{text:"Закрыть"}, 
				btnOk:{text:"Оформить", click: function() { submitForm('Y'); return false; }}
			});
			if($("[data-id='kontur_sale_order_confirm_has_errors']").length) kontur_sale_order_confirm_has_errors(true);
		});
		return false;
	}); 
});
</script>

----------------------------------------------------------------------------------

2) У кнопки подтверждения заказа убрать onclick="submitForm('Y'); return false;"
<a href="javascript:;" id="ORDER_CONFIRM_BUTTON" class="next_btn"><?=GetMessage("SOA_TEMPL_BUTTON")?><span class="fa fa-chevron-right"></span></a>