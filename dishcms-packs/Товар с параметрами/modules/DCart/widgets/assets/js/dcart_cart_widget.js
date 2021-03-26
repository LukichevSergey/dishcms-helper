/**
 * Скрипт инициализация для виджета корзины (DCart\widgets\CartWidget)
 * 
 */
$(function() {
	var $cart = $(".dcart-cart");
	
    $cart.find(".dcart-cart-count input[name='count']")
    	.live("keyup", DCartWidget.updateCount); //$.debounce(DCartWidget.updateCount, 800));
    
    $cart.find(".dcart-cart-btn-remove").live("click", DCartWidget.remove);
});