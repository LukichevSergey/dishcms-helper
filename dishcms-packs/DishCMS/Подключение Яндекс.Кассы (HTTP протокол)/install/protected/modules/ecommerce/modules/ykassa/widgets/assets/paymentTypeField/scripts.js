/**
 * Скрипт для виджета \ykassa\widgets\PaymentTypeField
 */
window.ykassa_widgets_PaymentTypeField=(function(){
	var _this={
		options: {}
	};
	
	// @function получить значение переменной
    function v(obj, prop, def) {
        if((typeof(obj)!="undefined") && (typeof(obj[prop])!="undefined")) return obj[prop];
        return (typeof(def)=="undefined") ? null : def;
    }
	// @function получить значение опции
    function o(name) {
        var value=_this.options;
        name.split(".").forEach(function(name){value=v(value,name);});
        return value;
    }
    
	_this.init=function(options){
		_this.options=options;
		$(document).on("click", ".ykassa__paymenttypes [data-payment-type]", _this.onPaymentType);
	};
	
	_this.onPaymentType=function(e){
		e.preventDefault();
		var type=$(e.target).data("payment-type");
		if(type) {
			$("#paymentType").val(type);
			$(o("jSubmit")).trigger("click");
		}
		return false;
	};
	
	return _this;
})();