/**
 * Скрипт инициализации виджета мини-корзины (DCart\widgets\MiniCartWidget)
 */
$(function() {
	var $miniCart = $(".dcart-mini-cart");
    var shop_module = $('.dcart-mini-cart .module');

    function hoverCartModule() {
        var self = $(shop_module);
        if ($(self).hasClass('open') || $(self).hasClass('empty'))
            return;

        $(self).toggleClass('hover');

        var module_main = $(self).find('.module-main');
        if ($(self).hasClass('hover')) {
            $(module_main).animate({'padding-top': '+=6px'}, 'fast');
        } else {
            $(module_main).animate({'padding-top': '-=6px'}, 'fast');
        }
    }
    $(shop_module).live('hover', $.throttle(hoverCartModule, 1000));
    
    $('.cart-open-link, .module-head, #cart-minimize', $('.dcart-mini-cart')).click(function() {
        if (!$('#open-cart').is(':focus') && !$(shop_module).hasClass('empty'))
            $(shop_module).toggleClass('open');
    });

    var buttons = $('body .shop-button');

    $(buttons).live('mousedown mouseup', function() {
        $(this).toggleClass('click');
    });
    $(buttons).live('mouseleave', function() {
        $(this).removeClass('click');
    });
    
    $miniCart.find(".dcart-mini-cart-btn-clear").live("click", function(e) {
    	if(confirm("Очистить корзину?")) {
    		e.preventDefault();
    		DCartMiniWidget.clear(e);
    	}
    	return false;
    });
    
    $miniCart.find(".dcart-mini-cart-item-count input[name='count']")
    	.live('keyup', DCartMiniWidget.updateCount); //$.debounce(DCartMiniWidget.updateCount, 800));
});