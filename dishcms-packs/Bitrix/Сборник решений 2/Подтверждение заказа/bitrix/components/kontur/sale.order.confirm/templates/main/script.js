function kontur_sale_order_confirm_has_errors(hasErrors) {
	if(hasErrors === true) {
	    $("#popup-window-content-konturpopupwin").parent().find(".popup-window-button-accept").hide();
    	var $cancel=$("#popup-window-content-konturpopupwin").parent().find(".popup-window-button-cancel");
	    $cancel.removeClass("popup-window-button-cancel");
    	$cancel.addClass("popup-window-button-decline");
	    $cancel.html("Не все обязательные данные заполнены. Продолжить оформление.");
	}
}
