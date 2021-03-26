;window.konturSbiForm=(function(){
	var _this={
		params: {},
		amount: 0,
		creditors: 0,
		fields: ["promocode", "name", "email", "phone", "passport_number", "passport_org", "passport_date", "passport_address", "date", "agree"],
		creditor_fields: ["creditor_name", "creditor_number", "creditor_date", "creditor_address"],
		_ajax: null,
		_creditor: null
	};
	
	/**
	 * Get param
	 */
	function p(name) {
		if(typeof(_this.params[name]) != "undefined") {
			return _this.params[name];
		}
		console.log("Parameter " + name + " not found");
	}
	
	/** 
	 * Get form jQuery selector.
	 * @param string path 
	 */
	function fs(path) {
		return "." + p("JS_FORM") + " " + path;
	}
	
	/** 
	 * Get form field
	 * @param string name param name.
	 */
	function f(name) {
		return $(fs("[name='" + name + "']"));
	}
	
	/** 
	 * Get form field value
	 * @param string name param name.
	 */
	function v(name) {
		var $f=f(name);
		if($f.is(":checkbox")) return $f.is(":checked") ? 1 : 0;
		return $f.val();
	}
	
	/** 
	 * Set form field error
	 * @param string name param name.
	 */
	function error(name, hasError) {
		if(hasError) f(name).addClass("error");
		else f(name).removeClass("error");
	}
	
	function datemask($date) {
		$.mask.definitions['d']='[0-3]';
		$.mask.definitions['m']='[0-1]';
		$.mask.definitions['y']='[1-2]';
		$date.mask("d9/m9/y999");
	}
	
	_this.updatePrice=function() {
		var total=_this.amount;
		if(_this.creditors > 0) total += (+p("PRICE_CREDITOR") * _this.creditors); 
		$(fs(p("PRICE"))).text(total);
	};
	
	_this.init=function(params) {
		_this.params=params;
		$(document).on("click", fs(":submit"), _this.onSubmit);
		$(document).on("change, keyup", fs("[name='promocode']"), _this.onChangePromocode);
		$(document).on("click", fs(".js-add-creditor"), _this.onClickAddCreditor);
		$(document).on("click", fs(".js-remove-creditor"), _this.onClickRemoveCreditor);
		
		_this.amount=+p("PRICE_DEFAULT");
		_this.updatePrice();
		
		_this._creditor=$(fs(".js-creditor-block")).clone();
		
		f("phone").mask("+7 (999) 999-99-99")
		f("passport_number").mask("99-99 999999");
		datemask(f("date"));
		datemask(f("passport_date"));
		datemask(f("creditor_date"));
		f("date").val(p("CURRENT_DATE"));
	};
	
	_this.onClickAddCreditor=function(e) {
		var $creditor=$(fs(".js-creditor-block:last")).after(_this._creditor.clone());
		$(fs(".js-creditor-block:last")).append('<a href="javascript:;" class="js-remove-creditor">убрать</a>');
		datemask(f("creditor_date"));
		
		_this.creditors++;
		_this.updatePrice();
		
		e.preventDefault();
		return false;
	}
	
	_this.onClickRemoveCreditor=function(e) {
		$(e.target).closest(".js-remove-creditor").parent().remove();
		
		_this.creditors--;
		_this.updatePrice();
		
		e.preventDefault();
		return false;
	};
	
	_this.onChangePromocode=function(e) {
		if(_this.ajax) _this.ajax.abort();
		_this.ajax = $.post(p("AJAX_URL"), {mode: "promocode", promocode: v("promocode"), params: p("PARAMS")}, function(response) {
			_this.amount=response.success ? +p("PRICE_PROMOCODE") : +p("PRICE_DEFAULT");
			_this.updatePrice();
		}, "json");
	};
	
	_this.onSubmit=function(e) {
		e.preventDefault();
		var data={mode: "validate", creditors: []};		
		_this.fields.forEach(function(name){ data[name]=v(name); });
		$(fs(".js-creditor-block")).each(function() {
			var $creditor=$(this), creditor_data={};
			_this.creditor_fields.forEach(function(name){
				creditor_data[name]=$creditor.find("[name='"+name+"']").val();
			});
			data.creditors.push(creditor_data);
		});
		
		if(_this.ajax) _this.ajax.abort();
		_this.ajax = $.post(p("AJAX_URL"), data, function(response) {
			_this.fields.forEach(function(name){ error(name, (typeof(response[name])!="undefined") && !response[name]); });
			response.creditors.forEach(function(creditor, idx) {
				_this.creditor_fields.forEach(function(name) {
					var $field=$(fs(".js-creditor-block:eq(" + idx + ") [name='" + name + "']")); 
					if((typeof(creditor[name])!="undefined") && !creditor[name]) $field.addClass("error");
					else $field.removeClass("error");
				});
			});
			
			if(response.success) {
				data.mode="payment";
				data.params=p("PARAMS");
				data.url=window.location.origin.replace('.local', '') + window.location.pathname;
				
				if(_this.ajax) _this.ajax.abort();
				_this.ajax = $.post(p("AJAX_URL"), data, function(response) {
					if(response.formUrl) {
						$(".js-payment-form-wrapper").hide();
						$(".js-payment-success").show();
						// window.location.href=response.formUrl;
					}
				}, "json");
			}
		}, "json");
		
		return false;
	};	
	
	function showSuccessfulPurchase(order) {
		console.log('success', order);
	}
	function showFailurefulPurchase(order) {
		console.log('fail', order);
	}
	
	return _this;
})();