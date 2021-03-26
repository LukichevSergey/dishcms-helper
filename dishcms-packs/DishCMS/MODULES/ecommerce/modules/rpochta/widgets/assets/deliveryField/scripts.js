/**
 * Javascript для виджета \rpochta\widgets\DeliveryField
 * 
 */
window.rpochta_widgets_DeliveryField=(function() {
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
    
    _this.isOpsMode=function() {
        return (v(o("mode_types"), j("mode", ":checked").val(), o("default_mode_type")) == "ops");
    };
    
    _this.autoCompleteRebuildIntervalId=null;
    
     /**
     * Инициализация
     * 
     * @param options параметры
     * "mode_types": {mode: type}, где type: "ops", "address"
     * "default_mode_type": type, где type: "ops", "address" 
     * "model_name": string
     */    
    _this.init=function(options) {
        _this.options=options;
        $box=$(".rpochta__box");
        
        $box.on("change", "[data-js='mode']", _this.onChangeMode);
        $box.find("[data-js='mode']:checked").trigger("change");
        
        $(document).on("change", ".rpochta__box .rpochta__param [data-js='rpo_category']", _this.onChangeParam);
        $(document).on("change", ".rpochta__box .rpochta__param [data-js='rpo_type']", _this.onChangeParam);
        $(document).on("change", ".rpochta__box .rpochta__param [data-js='payment_type']", _this.onChangeParam);
        $(document).on("keyup", ".rpochta__box .rpochta__city .chosen-search :text", _this.onCityToKeyUp);
        $(document).on("change", ".rpochta__box [data-js='cityname']", _this.onCityToChange);
        
        $(".rpochta__box .rpochta__param [data-js='rpo_type']:checked").trigger("change");
        $(".rpochta__box [data-js='cityname']").trigger("change");
    };
    
    _this.onCityToKeyUp=function(e) {
        if(_this.autoCompleteRebuildIntervalId) clearInterval(_this.autoCompleteRebuildIntervalId);
        _this.autoCompleteRebuildIntervalId=setTimeout(function(){
           $.get("/ecommerce/cdek/city/autocomplete", {"cityname":$(e.target).val(), "json":"on", "html":"on", "postcode":"on"}, function(r) {
                jm("index_to").html($.parseHTML(r.html));
                jm("index_to").chosen({action:"update", search:$(e.target).val()});                    
                jm("index_to").trigger("change");
            }, "json");
        }, 500);
    };
    
    _this.onCityToChange=function(e) {
        if(_this.autoCompleteRebuildIntervalId) clearInterval(_this.autoCompleteRebuildIntervalId);
        _this.calcInProccess=false;
        window.rpochta_widgets_OpsField.updateOpsInfo();
        if(!_this.isOpsMode() || jm("ops_address").val().length) {
            _this.calc();
        }
    };
    
    _this.onChangeMode=function(e) {
        if(_this.isOpsMode()) {
            j("info").hide();
            j("address").hide();
            j("ops").show();
            j("rpochta-ops-btn").show();
            if(jm("ops_address").val().length) {
                _this.calc();
            }
        }
        else {
            j("ops").hide();
            j("rpochta-ops-btn").hide();
            j("address").show();
            _this.calc();
        }
    };
    
    _this.onChangeParam=function(e) {
    	$.cookie($(e.target).attr("name"), $(e.target).val(), {path:"/"});        
        _this.calc();
    };
    
    _this.calcInProccess=false;
    _this.calc=function() {
        if(!_this.calcInProccess) {
            _this.calcInProccess=true;
            j("info").hide();
            if(jm("index_to").val() && jm("index_to").val().length) {
                var index_to=((_this.isOpsMode() && jm("ops_index").val())) ? jm("ops_index").val() : jm("index_to").val();
                $.get(
                    "/ecommerce/rpochta/calc", 
                    {
                        "index_to": index_to, 
                        "mail_category": j("rpo_category", ":checked").val(),
                        "mail_type": j("rpo_type", ":checked").val(),
                        "payment_method": j("payment_type", ":checked").val()
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
