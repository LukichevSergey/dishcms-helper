/**
 * DCartMiniWidget class
 * Класс для виджета мини-корзины (DCart\widgets\MiniCartWidget)
 * 
 */
if(typeof(DEBUG_MODE) == 'undefined') {
	var DEBUG_MODE = false;
}

var DCartMiniWidget = {
	/**
	 * Обновление данных корзины
	 * @param object data
	 * data.miniCartSummary: Блок общей информации
	 * data.miniCartItems: Блок списка товаров
	 * @param boolean isEmpty Пуста корзина или нет.
	 */
	update: function(data) {
		if(typeof(data.miniCartSummary) != 'undefined') {
			$(".dcart-mini-cart .dcart-mini-cart-summary").html(data.miniCartSummary);
		}
		if(typeof(data.miniCartItems) != 'undefined') {
			$(".dcart-mini-cart .dcart-mini-cart-items").html(data.miniCartItems);
		}
		
		if((typeof(data.cartHashes) != 'undefined') && !data.cartHashes.length) {
			$('.dcart-mini-cart .module').addClass('empty').removeClass('open hover');
	        $('.dcart-mini-cart .module-main').css('padding-top', 0);
		}
		else {
			$('.module', $('#shop-cart')).removeClass('empty');
		}
	},
	
	/**
	 * Обновить количество
	 */
	updateCount: function(e) {
		$target = $(e.target);
		
		DCart.updateCount(e, function(json) {
			if(json.success) {
				DCartMiniWidget.update(json.data);
				if(typeof(DCartWidget) == 'object') DCartWidget.update(json.data);
			}
			else DCart.showErrors(json);
		});
		
		return false;
	},
	
	/**
	 * Очистка корзины
	 */
	clear: function(event) {
		var handler = function(json) {
			if(json.success) {
				DCartMiniWidget.update(json.data);
				if(typeof(DCartWidget) == 'object') DCartWidget.update(json.data);
			}
			else 
				DCart.showErrors(json);
		};

		DCart.clear($(event.target).attr("href"), handler);
		
		return true;
	}
}