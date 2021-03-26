/**
 * Javascript для виджета \pecom\widgets\DeliveryField
 * 
 */
window.pecom_widgets_DeliveryField=(function() {
    var _this={
        options: {}
    };    
    var $box;
    
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
    // @function получить элемент поля атрибута модели
    function m(attribute) {
        return '#'+o("model_name") + "_" + attribute;
    }
    
     /**
     * Инициализация
     * 
     * @param options параметры
     * "model_name": string
     * "attribute": string
     */    
    _this.init=function(options) {
        _this.options=options;
        $box=$(".pecom__box");
        
        $(document).on("change", ".pecom__box [data-js='cityname']", _this.onChangeCity);
        
        $(".pecom__box [data-js='cityname']").trigger("change");
    };
    
    _this.autoCompleteRebuildIntervalId=false;
    _this.onChangeCity=function(e) {
        if(_this.autoCompleteRebuildIntervalId) clearInterval(_this.autoCompleteRebuildIntervalId);
        _this.calcInProccess=false;
        _this.calc();
    };
    
    _this.calcInProccess=false;
    _this.calc=function() {
        if(!_this.calcInProccess) {
            _this.calcInProccess=true;
            j("info").hide();
            if(j("cityname").val()) {
                $.post(
                    "/ecommerce/pecom/calc", 
                    {
                        'deliver': {
                            'town': j("cityname").val()
                        }
                    }, 
                    function(r) {
                        j("info").html($.parseHTML(r.data.html));
                        j("info").show();
                    }, 
                    "json"
                );
            }
            _this.calcInProccess=false;
        }
    };
    
    return _this;
})();
