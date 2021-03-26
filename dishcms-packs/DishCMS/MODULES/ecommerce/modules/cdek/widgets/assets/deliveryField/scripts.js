/**
 * Javascript для виджета \cdek\widgets\DeliveryField
 * 
 */
window.cdek_widgets_DeliveryField=(function() {
    var defaultCdekVariant=13;
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
    
    _this.isPvzMode=function() {
        return (v(o("mode_types"), j("mode", ":checked").val(), o("default_mode_type")) == "pvz");
    };
    
    _this.getRecCityId=function() {
        return jm("rec_city_id").val();
    }
    
    /**
     * Инициализация
     * 
     * @param options параметры
     * "mode_types": {mode: type}, где type: "pvz", "address"
     * "default_mode_type": type, где type: "pvz", "address" 
     * "model_name": string
     */
    _this.autoCompleteRebuildIntervalId=null;
    _this.init=function(options) {
        _this.options=options;
        $box=$(".cdek__box");
        $box.on("change", "[data-js='mode']", _this.onChangeMode);
        $box.find("[data-js='mode']:checked").trigger("change");
        
        $box.on("keyup", ".cdek__city .chosen-search :text", function(e) {
            if(_this.autoCompleteRebuildIntervalId) clearInterval(_this.autoCompleteRebuildIntervalId);
            _this.autoCompleteRebuildIntervalId=setTimeout(function(){
               $.get("/ecommerce/cdek/city/autocomplete", {"cityname":$(e.target).val(), "json":"on", "html":"on"}, function(r) {
                    jm("rec_city_id").html($.parseHTML(r.html));
                    jm("rec_city_id").chosen({action:"update", search:$(e.target).val()});                    
                    jm("rec_city_id").trigger("change");
                }, "json");
            }, 500);
        });
        
        $(document).on("change", m("rec_city_id"), function(e) {
            if(_this.autoCompleteRebuildIntervalId) clearInterval(_this.autoCompleteRebuildIntervalId);
            _this.calcInProccess=false;
            jm("pvz_code").val("");
            window.cdek_widgets_PvzField.updatePvzInfo();
            if(!_this.isPvzMode() || jm("pvz_code").val().length) {
                _this.calc();
            }
        });
        
        //$(".cdek__box [data-js='cityname']").trigger("change");
    };
    
    _this.onChangeMode=function(e) {
        if(_this.isPvzMode()) {
            j("info").hide();
            j("address").hide();
            j("pvz").show();
            j("cdek-pvz-btn").show();
            if(jm("pvz_code").val().length) {
                _this.calc();
            }
        }
        else {
            j("pvz").hide();
            j("cdek-pvz-btn").hide();
            j("address").show();
            _this.calc();
        }
    };
    
    _this.calcInProccess=false;
    _this.calc=function() {
        if(!_this.calcInProccess) {
            _this.calcInProccess=true;
            if(_this.getRecCityId()) {
                $.post("/ecommerce/cdek/calc", {rec:_this.getRecCityId(), mode:j("mode", ":checked").val()}, function(r) {
                    j("info").html($.parseHTML(r.data.html));
                    j("info").show();
                }, "json");
            }
            _this.calcInProccess=false;
        }
    };
    
    return _this;
})();
