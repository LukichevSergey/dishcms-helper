/**
 * DCartWidget class
 * Скрипт класса для виджета корзины (DCart\widgets\CartWidget)
 * 
 */
if(typeof(DEBUG_MODE) == 'undefined') {
	var DEBUG_MODE = false;
}

var DCartWidget = {
	/**
	 * Отображен виджет корзины или нет
	 */
	isExists: function() {
		return $(".dcart-cart").length;
	},
	
	/**
	 * Добавление дополнительного параметра к отправляемым данным, 
	 * указывающий, что данный виджет корзины отображен на сайте.
	 * @param object data отправляемые данные. 
	 */
	prepareData: function(data) {
		if(DCartWidget.isExists()) {
			data['dcart-cart-widget'] = true;
		}
	},
	
	/**
	 * Обработчик обновления количества
	 */
	updateCount: function(e) {
		$target = $(e.target);
		
		DCart.updateCount(e, function(json) {
			if(json.success) {
				if(typeof(DCartMiniWidget) == 'object') DCartMiniWidget.update(json.data);
				DCartWidget.update(json.data);
			}
			else DCart.showErrors(json);
		});
		
		return false;
	},
	
	/**
	 * Обновление отображения корзины
	 */
	update: function(data) {
		if(typeof(data.cartItems) != 'undefined') {
			$(".dcart-cart .dcart-cart-items").html(data.cartItems);
		}
		
		if(typeof(data.cartTotalPrice) != 'undefined') {
			$(".dcart-cart .dcart-cart-total-price").html(data.cartTotalPrice);
		}
		
		if(typeof(data.cartHashes) != 'undefined') {
			if(data.cartIsFirst || (!data.cartHashes.length && DCartWidget.isExists())) {
				window.location.reload();
			}
			$(".dcart-cart .dcart-cart-item").each(function() {
				if($.inArray($(this).data("item-hash"), data.cartHashes) < 0) {
					$(this).remove();
				}
			});
		}
	},
	
	/**
	 * Обработчик удаления товара 
	 */
	remove: function(e) {
		if(confirm("Удалить товар из корзины?")) {
			DCart.remove("/dCart/remove", $(e.target).data("item-hash"), function(json) {
				if(json.success) {
					if(typeof(DCartMiniWidget) == 'object') DCartMiniWidget.update(json.data);
					DCartWidget.update(json.data);
				}
				else 
					DCart.showErrors(json);
			});
		}
		
		return false;
	}
}


