;window.konturSbiForm=(function(){
	var _this={
		params: {}
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
		return $(fs("[name='" + p(name) + "']"));
	}
	
	/** 
	 * Get form field value
	 * @param string name param name.
	 */
	function v(name) {
		return f(name).val();
	}
	
	/** 
	 * Set form field error
	 * @param string name param name.
	 */
	function error(name, hasError) {
		if(hasError) f(name).addClass("error");
		else f(name).removeClass("error");
	}
	
	_this.init=function(params) {
		_this.params=params;
		$(document).on("click", fs(":submit"), _this.onSubmit);
	};	
	
	_this.onSubmit=function() {
		var data={
			mode: "validate",
			name: v("FIELD_NAME"),
			amount: v("FIELD_AMOUNT"),
			phone: v("FIELD_PHONE"),
			email: v("FIELD_EMAIL")
		};
		$.post(p("AJAX_URL"), data, function(response) {
			error("FIELD_NAME", !response.name); 
			error("FIELD_AMOUNT", !response.amount); 
			error("FIELD_PHONE", !response.phone); 
			error("FIELD_EMAIL", !response.email); 
			if(response.success) {
				data.mode="payment";
				data.params=p("PARAMS");
				data.url=window.location.origin.replace('.local', '') + window.location.pathname;
				$.post(p("AJAX_URL"), data, function(response) {
					if(response.formUrl) {
						window.location.href=response.formUrl;
					}
				}, "json");
			}
		}, "json");
	};	
	
	function showSuccessfulPurchase(order) {
		console.log('success', order);
	}
	function showFailurefulPurchase(order) {
		console.log('fail', order);
	}
	
	return _this;
})();