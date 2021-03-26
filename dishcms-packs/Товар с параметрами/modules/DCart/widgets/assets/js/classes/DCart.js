/**
 * Класс DCart
 * 
 * @use json_decode.js http://phpjs.org/functions/json_decode/
 * @use DCartHelper.js
 */
if(typeof(DEBUG_MODE) == 'undefined') {
	var DEBUG_MODE = false;
}

var DCart = {
	/**
	 * Добавление товара в корзину
	 * @param string url Ссылка на действие добавления в корзину
	 * @param object data Дополнительные данные. (например: выбранный цвет).
	 * @param jQuery.Event event Объект события    
	 */
	add: function(url, data, event) {
		
		if((typeof(data) != 'object') && (typeof(data) != 'string')) 
			return false;
		
		if(typeof(data) == 'string') 
			data = json_decode(data);
		
		if(!data) return false;
		
		var $eventTarget = null;
		if(event instanceof jQuery.Event) {
			if($.inArray($(event.target).prop("tagName"), ["A", "INPUT", "BUTTON"]) > -1) {
				$eventTarget = $(event.target);
			}
			else if($(event.target).parents("a,button,input:first").length) {
				$eventTarget = $(event.target).parents("a,button,input:first");
			}
		}
		
		var hasError = false;
		var _data = {};
		for(var key in data) {
			if($(data[key]).length) {
				var $promtError = $(data[key]).siblings(".dcart-propmt-error"); 
				if(!$promtError.length) {
					$promtError = $('<div class="dcart-propmt-error" style="display:none"></div>').insertBefore($(data[key]));
				}
				
				var value = $(data[key]).val();
				if(!value && (typeof(event) == 'object')) {
					$promtError.html($(data[key]).attr("data-prompt-alert"));
					$promtError.show();
					hasError = true;
				}
				else $promtError.hide();
				
				_data[key] = value;
			}
		}
		
		if(!hasError) { 
			var data = { data: _data };
			
			if(event instanceof jQuery.Event) {
				data.model = $eventTarget.attr("data-dcart-model");
				if(!data.model && DEBUG_MODE) console.log('Warning: (DCartWidget.js) Model not defined.');
			} 
			
			if(typeof(DCartWidget) == 'object') 
				DCartWidget.prepareData(data);
			
			$.post(url, data, function(json) {
				if(typeof(json) != 'object') {
					if(DEBUG_MODE) console.log('Warning: (DCartWidget.js) Empty server responce.')
					return false;
				}
				
				if(json.success) {
					$.prompt("Товар добавлен в корзину!", {buttons: [], opacity: 0.8, top: "30%", timeout: 1500, persistent: false});
					if(typeof(DCartWidget) == 'object') DCartWidget.update(json.data);
					if(typeof(DCartMiniWidget) == 'object') DCartMiniWidget.update(json.data);
				}
				else 
					DCart.showErrors(json);
	        }, "json");
		}
		
		return !hasError;
	},
	
	/**
	 * Получить количество товара в корзине
	 * @param string hash хэш товара в корзине.
	 */
	getCount: function(hash) {
		var count = -1;
		
		$.ajax({
			url: "/dCart/getCount",
			type: "post",
			async: false,
			data: {hash: hash}, 
			dataType: "json",
			success: function(json) {
				if(json.success) 
					count = json.data.count; 
				else 
					DCart.showErrors(json);
			}
		});
		
		return count;
	},
	
	/**
	 * Обновить количество
	 * @param jQuery.Event e объект события. 
	 * @param function successHandler callback обработчик. 
	 */
	updateCount: function(e, handlerSuccess) {
		var $target = $(e.target);
		var count = +$target.val();
		if(isNaN(count)) {
			$target.addClass("dcart-error-input");
			return false;
		}
		else {
			$target.removeClass("dcart-error-input");
		}
		
		if (count <= 0) {
			if(!confirm('Вы хотите удалить товар из корзины?')) {
				return false;
			}
	    }
		else if(count < 0) {
			var count = DCart.getCount($target.data("item-hash"));
			if(count > 0) 
				$target.val(count);
			else
				return false;
		}
	    
		data = {hash: $target.data("item-hash"), count: $target.val()}; 
		if(typeof(DCartWidget) == 'object') 
			DCartWidget.prepareData(data);
		
		$.post("/dCart/updateCount", data, handlerSuccess, "json");
		
		return false;
	},
	
	/**
	 * Очистка корзины
	 * @param string url ссылка на действие удаления 
	 * @param string hash хэш удаляемого из корзины товара 
	 * @param function successHandler callback обработчик. 
	 */
	remove: function(url, hash, successHandler)
	{
		if(typeof(successHandler) != 'function') {
			successHandler = function(json) {
				if(json.success) {
					window.location.reload(); 
				}
				else 
					DCart.showErrors(json);
			};
		}
			
		$.post(url, {hash: hash}, successHandler, "json"); 
	},
	
	/**
	 * Очистка корзины
	 * @param string url ссылка на действие очистки
	 * @param function successHandler callback обработчик. 
	 */
	clear: function (url, successHandler) {
		if(typeof(successHandler) != 'function') {
			successHandler = function(json) {
				if(json.success) {
					window.location.reload(); 
				}
				else 
					DCart.showErrors(json);
			};
		}
			
		$.post(url, {clear: 'clear'}, successHandler, "json"); 
	},
	
	/**
	 * Обработка ошибок по умолчанию
	 * @param object json Данные после ajax запроса.
	 */
	showErrors: function(json) {
		if(!json.success) {
			if(json.errors.length > 0) {
				$(json.errors).each(function(i) {
					console.log(json.errors[i]);
				});
			} else {
//				alert(json.errorDefaultMessage);
				console.log(json.errorDefaultMessage);
			}
		}
	}
}