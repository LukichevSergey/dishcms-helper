;window.konturSbiForm=(function(){
	var _this={
		params: {},
		amount: 0,
		fields: ["promocode", "name", "email", "phone", "passport_number", "passport_org", "passport_date", "passport_address", "date", "agree"],
		_creditors: [],
		_ajax: null,
		_canExecuteTime: 0
	};
	
	function prevent(e) {
		e.preventDefault();
		return false;
	}
	
	function url() {
		return window.location.origin.replace('.local', '') + window.location.pathname;
	}
	
	/**
	 * Get param
	 * @param string name param name
	 * @return mixed
	 */
	function p(name) {
		if(typeof(_this.params[name]) != "undefined") {
			return _this.params[name];
		}
		console.log("Parameter " + name + " not found");
	}
	
	/**
	 * Get field attribute "name" jQuery selector
	 * @param string name field "name" value.
	 * @return string
	 */
	function jn(name) {
		return "[name='" + name + "']";
	}
	
	/**
	 * Get jQuery object
	 * @param string path by current form.
	 * @return jQuery object
	 */
	function j(path) {
		return $(fs(path));
	}
	
	/**
	 * Get jQuery selector
	 * @param string path jQuery selector 
	 * or form field name
	 */
	function jp(path) {
		if((/^[a-z0-9_]+$/gi).test(path)) return jn(path);
		return path;
	}
	
	/** 
	 * Get jQuery selector by current "form".
	 * @param string path jQuery selector 
	 * or form field name 
	 */
	function fs(path) {
		return "." + p("JS_FORM") + " " + jp(path);
	}
	
	/** 
	 * Get jQuery object form field
	 * @param string name field name.
	 */
	function f(name) {
		return j(jn(name));
	}
	
	/** 
	 * Get form field value
	 * @param string name field name.
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
	
	/**
	 * Init date mask and calendar
	 * @param object jQuery object of "date" field
	 */
	function datemask($date) {
		$.mask.definitions['d']='[0-3]';
		$.mask.definitions['m']='[0-1]';
		$.mask.definitions['y']='[1-2]';
		$date.mask("d9.m9.y999");
		if ($(window).width() >= 992) {
			$date.on("click", function() {
				BX.calendar({node: this, field: this, bTime: false});
			});
		}
	}
	
	/**
	 * Can send ajax request?
	 * @return bool
	 */
	function canExecute() {
		if(_this._canExecuteTime < (Date.now() - 200)) {
			_this._canExecuteTime=Date.now();
			return true;
		}
		return false;
	}
	
	function ajax(mode, data, handler) {
		data["mode"]=mode;
		if(_this._ajax) _this._ajax.abort();
		_this._ajax=$.post(p("AJAX_URL"), data, handler, "json");
	}
	
	/**
	 * Creditor class
	 * @param integer id ID creditor block
	 */
	_this._creditor=[];
	function Creditor() {
		var _t={			
			id: 0,
			fields: ["creditor_name", "creditor_number", "creditor_date", "creditor_address"],
			_idx: -1
		};
		
		function generateId() {
			var id=0, $creditors=j(".js-creditor-block");
			
			if(($creditors.length === 1) && (+$creditors.eq(0).attr("data-id") === 0)) {
				return 0;
			}
			
			j(".js-creditor-block").each(function(){
				if(+$(this).attr("data-id") > id) id=+$(this).attr("data-id");
			});
			
			return (id + 1);
		}
		
		function c() {
			return fs(".js-creditor-block[data-id='" + _t.id + "'] ");
		}
		
		function jc() {
			return $(c());
		}
		
		function cf(path) {
			return c() + jp(path);
		}
		
		function jcf(path) {
			return $(cf(path));
		}
		
		function cv(name) {
			return jcf(name).val();
		}
		
		function cl() {
			return j(".js-creditor-block:last");
		}
		
		function isINN(value) {
			return (!isNaN(+value) && ((value.length==10)||(value.length==12)));
		}
		
		_t.init=function() {
			_t.id=generateId();
			if(_t.id === 0) {
				_this._creditor=jc().clone();				
				jc().attr("data-id", 1);
				_t.id=1;
				$(document).on("click", fs(".js-add-creditor"), _t.onClickAdd);
			}
			else {
				var $creditor=_this._creditor.clone();
				$creditor.attr("data-id", _t.id);
				cl().after($creditor);
			}			
			
			$(document).on("change, keyup", cf("creditor_name"), _t.onKeyUpName);
			$(document).on("change, keyup", cf("input"+jn("creditor_address")), _this.onKeyUpAddress);
			$(document).on("change, keyup", cf("creditor_city"), _t.onKeyUpCity);
	
			$(document).on("click", cf(".js-remove-creditor"), _t.onClickRemove);
			
			datemask(jcf("creditor_date"));
			
			if(_t.id > 1) {
				jc().append('<a href="javascript:;" class="js-remove-creditor">убрать</a>');
			}
			
			_this._creditors.push(_t);
			
			_this.updatePrice();			
		};
		
		_t.getData=function() {
			var data={};
			_t.fields.forEach(function(name) { data[name]=cv(name); });
			return data;
		};
		
		_t.error=function(errors) {
			_t.fields.forEach(function(name) {
				if((typeof(errors[name])!="undefined") && !errors[name]) jcf(name).addClass("error");
				else jcf(name).removeClass("error");
			});
		};
		
		_t.showCity=function(inn) {
			var hint=(typeof inn != "undefined") && isINN(inn) ? "(ИНН: "+inn+")" : "";
			if(inn.length) jcf(".js-creditor-city-hint .js-creditor-city-hint-text").html(hint);
			jcf(".js-creditor-city").slideDown();
			jcf(".js-creditor-city-hint").slideDown();
		};
		
		_t.hideCity=function() {
			jcf("creditor_city").attr("data-inn", "").val("");
			jcf(".js-creditor-city").hide();
			jcf(".js-creditor-city-hint").hide();
		};
		
		_t.getAddressSelect=function() {
			var $select=jcf("creditor_address").siblings("select.js-creditor-address");
			if(!$select.length) {
				$select=$('<select class="js-creditor-address"></select>');
			}
			return $select;
		};
		
		_t.addAddressSelect=function(data, isInnMode) {
			var $select, $option;
			$select=_t.getAddressSelect();
			$select.find("option").remove();
			data.forEach(function(item) {
				$option=$("<option>" + item.address + " (" + item.name +")</option>")
					.attr("data-name", item.name)
					.val(item.address);
				$select.append($option);
			});
			
			jcf("creditor_address").hide();
			jcf("creditor_address").after($select);
			
			var eventName = ($select.find("option").length === 1) ? "mousedown" : "change";
			$select.on(eventName, _t.onChangeAddressSelect);
			if(typeof isInnMode == "undefined") {
				$select.trigger(eventName, {init: 1});
			}
		};
		
		_t.removeAddressSelect=function() {
			if(_t.getAddressSelect().length > 0) {
				_t.getAddressSelect().remove();
				jcf("creditor_address").show();				
			}
		};
		
		_t.onChangeAddressSelect=function(e, params) {
			var $selected=jcf("select.js-creditor-address option:selected");
			if($selected.length > 0) {
				jcf("creditor_address").val($selected.val());
				jcf("creditor_name").val($selected.attr("data-name"));
				if((typeof(params) == "undefined") || (typeof(params.init) == "undefined")) {
					_t.removeAddressSelect();
					_t.hideCity();
				}
			}
		};
		
		_t.onKeyUpName=function(e, params){
			var value=cv("creditor_name");
			var isInnMode=((typeof(params) != "undefined") 
				&& (typeof(params.city) != "undefined")
				&& jcf("creditor_city").attr("data-inn")
				&& (jcf("creditor_city").attr("data-inn").length > 0)
			) || isINN(value);
			
			if(isInnMode && !isINN(value)) {
				value=jcf("creditor_city").attr("data-inn");
			}
			
			if(value && (value.length > 0)) {
				var inn=value;
				var query=(value + " " + cv("creditor_city")).trim();
				ajax("company", {query: query}, function(response) {
					if(response.success && (response.data.length > 0)) {
						if(isInnMode) jcf("creditor_city").attr("data-inn", inn);
						if(response.data.length >= 20) _t.showCity(inn);
						_t.addAddressSelect(response.data, isInnMode);						
					}
					else if(!cv("creditor_address")) {
						_t.removeAddressSelect();
					}
				});				
			}
			else {
				_t.removeAddressSelect();
				_t.hideCity();
			}
			
			return prevent(e);		
		};
		
		_t.onKeyUpCity=function(e){
			if(canExecute() && (cv("creditor_city").trim().length > 1)) {
				jcf("creditor_name").trigger("keyup", {city: 1});
			}
			return prevent(e);
		};
		
		_t.onClickAdd=function(e) {
			new Creditor();
			return prevent(e);
		};		
	
		_t.onClickRemove=function(e) {
			var deleteIdx=-1;
			_this._creditors.forEach(function(creditor, idx) {
				if(creditor.id === _t.id) deleteIdx=idx;
			});
			
			if(deleteIdx > -1) {
				jc().remove();
				_this._creditors.splice(deleteIdx, 1);
			}
			
			_this.updatePrice();
			
			return prevent(e);
		};
		
		_t.init();
		
		return _t;
	}
	
	/**
	 * Initialization
	 * @param object params params as {
	 *	JS_FORM: (string) form jQuery selector,
     *  AJAX_URL: (string) AJAX URL,
     *  PRICE: (string) jQuery selector "price" text container,
     *  PRICE_DEFAULT: (float) default price,
     *  PRICE_PROMOCODE: (float) price with promocode,
     *  PRICE_CREDITOR: (fload) addition price per creditor,
     *  CURRENT_DATE: (string) current date by format "d.m.Y",
     *  PARAMS: (string) encrypt $arParams
	 * }
	 */
	_this.init=function(params) {
		_this.params=params;
		$(document).on("click", fs(":submit"), _this.onSubmit);		
		$(document).on("keyup", fs("promocode"), _this.onKeyUpPromocode);
		$(document).on("change, keyup", fs("passport_address"), _this.onKeyUpAddress);
		
		_this.amount=+p("PRICE_DEFAULT");
		_this.updatePrice();		
		
		f("phone").mask("+7 (999) 999-99-99")
		f("passport_number").mask("99-99 999999");
		datemask(f("date"));
		datemask(f("passport_date"));
		f("date").val(p("CURRENT_DATE"));
		
		new Creditor();
	};
	
	/**
	 * Update price
	 */
	_this.updatePrice=function() {
		var total=_this.amount;
		if(_this._creditors.length > 1) {
			total += (+p("PRICE_CREDITOR") * (_this._creditors.length - 1)); 
		}
		j(p("PRICE")).text(total);
	};
	
	/**
	 * OnSubmit
	 * @param Event e
	 */
	_this.onSubmit=function(e) {
		var data={creditors: []};		
		_this.fields.forEach(function(name){ data[name]=v(name); });
		_this._creditors.forEach(function(creditor){ data.creditors.push(creditor.getData()); });
		ajax("validate", data, function(response) {
			_this.fields.forEach(function(name){ error(name, (typeof(response[name])!="undefined") && !response[name]); });
			response.creditors.forEach(function(errors, idx) {
				if(typeof _this._creditors[idx] !== "undefined") _this._creditors[idx].error(errors);
			});
			
			if(response.success) {
				data["params"]=p("PARAMS");
				data["url"]=url();
				ajax("payment", data, function(response) {
					if(response.formUrl) {
						$(".js-payment-form-wrapper").hide();
						$(".js-payment-success").show();
						window.location.href="/disclaimer/payment/?order=" + response.formUrl;
					}
				});
			}
		});		
		return prevent(e);
	};
	
	/**
	 * onChangePromocode
	 * @param Event e
	 */
	_this.onKeyUpPromocode=function(e) {
		ajax("promocode", {promocode: v("promocode"), params: p("PARAMS")}, function(response) {
			_this.amount=response.success ? +p("PRICE_PROMOCODE") : +p("PRICE_DEFAULT");
			_this.updatePrice();
		});
	};
	
	_this.onKeyUpAddress=function(e) {
		var $address=$(e.target);
		if(canExecute() && ($address.val().length > 4)) {
			ajax("address", {address: $address.val()}, function(response) {
				var $hint=$address.siblings(".js-creditor-address-hint");
				if(!$hint || !$hint.length) {
					$hint=$('<ul class="js-creditor-address-hint"></ul>');
					$hint.hide();
					$hint.on("click", "li", function(e) { $address.val($(e.target).closest("li").text()); $hint.hide(); });
					$hint.on("mouseleave", function(e){ $hint.hide(); });
					$address.after($hint);
				}
				
				if(response.success && (response.data.length > 0)) {
					$hint.find("li").remove();
					response.data.forEach(function(address){ $hint.append("<li>" + address + "</li>"); });
					$hint.show();
				}
				else {
					$hint.hide();
				}
			});
		}
		return prevent(e);
	};
	
	return _this;
})();
