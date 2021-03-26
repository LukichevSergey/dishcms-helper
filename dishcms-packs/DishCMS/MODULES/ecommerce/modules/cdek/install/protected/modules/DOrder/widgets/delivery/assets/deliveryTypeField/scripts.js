/**
 * Javascript для виджета \DOrder\widgets\DeliveryTypeField
 * 
 */
window.DOrder_widgets_DeliveryTypeField=(function() {
    var _this={
        options: {}
    };    
    
    // @function получить значение переменной
    function v(obj, prop, def) {
        if((typeof(obj)!="undefined") && (typeof(obj[prop])!="undefined")) return obj[prop];
        return (typeof(def)=="undefined") ? null : def;
    }
    // @function получить элемент по data-js
    function j(name, filter) {
        $elm=$box.find("[data-js='"+name+"']");
        if($elm && (typeof(filter)!="undefined")) return $elm.filter(filter);
        return $elm;
    }
    // @function получить значение опции
    function o(name) {
        var value=_this.options;
        name.split(".").forEach(function(name){value=v(value,name);});
        return value;
    }
    // @function получить элемент поля атрибута модели
    function jm(attribute) {
        return $('#'+o("model_name") + "_" + attribute);
    }
    
    /**
     * Инициализация
     * 
     * @param options параметры
     * "mode_types": {mode: type}, где type: "pvz", "address"
     * "default_mode_type": type, где type: "pvz", "address" 
     * "model_name": string
     */
    _this.init=function(options) {
        _this.options=options;
        $(document).on("change", "[name='"+o("field_name")+"']", function(e) {
            var v=$(e.target).val();
            $.cookie(o("cookie_name"), v, {path:"/"});
            $("[id^='delivery_type-']").hide();
            if($("[id^='delivery_type-"+v+"']").length) { 
                $("[id^='delivery_type-"+v+"']").show();
            }
        });
        var $deliveryType=$("[name='"+o("field_name")+"']");
        if($deliveryType.is(":checked")) $deliveryType.filter(":checked").trigger("change");
        else $deliveryType.eq(1).click();
    };    
    
    return _this;
})();
