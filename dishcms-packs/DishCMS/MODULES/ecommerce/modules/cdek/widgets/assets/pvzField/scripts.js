/**
 * Скрипт для виджета \cdek\widgets\PvzField
 */
window.cdek_widgets_PvzField=(function(){
    var defaultCdekPvzVariant=11;
    var _this={
        map: null,
        cache: {},
        options: {},
        initialized: false
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
        return $(m(attribute));
    }
    // @function получить элемент поля атрибута модели
    function m(attribute) {
        return '#'+o("model_name") + "_" + attribute;
    }
    function is(obj) {
        var length=0; for(var k in obj) length++; return (length > 0);
    }
    
    /**
     * Инициализация
     * @param array options
     *  "model_name"
     * 
     * 
     *  "urlGetPvzList" URL получения списка ПВЗ
     *  "jCity" выражение выборки элемента с идентификатором города СДЭК для которого генерится карта ПВЗ.
     *  "jPvzButton" выражение выборки элемента открытия окна выбора ПВЗ.
     *  "jPvzContent" выражение выборки элемента выбора ПВЗ.
     *  "jPvzMap" выражение выборки элемента в котором будет отображена карта ПВЗ.
     */
    _this.init=function(options) {
        _this.options=options;
        $box=$(".cdek__box");
        
        j(o("jPvzButton")).html($.parseHTML('&nbsp;<span data-js="cdek-pvz-info"></span>&nbsp;<a data-js="cdek-pvz-btn-open" href="javascript:;">выбрать</a>'));
        
        $(document).on("click", _this.getPvzButtonJExpr(), _this.openPvzList);
        
        $box.on("change", "[data-js='mode']", _this.onChangeMode);
        $(document).on("change", m("rec_city_id"), _this.onChangeRecCity);
        
        _this.updatePvzInfo();
    };
    
    _this.getPvzButtonJExpr=function() {
        return "[data-js='"+o("jPvzButton")+"'] a";
    };
    
    _this.onChangeMode=function(e) {
        if(window.cdek_widgets_DeliveryField.isPvzMode() && !_this.getPvzCode()) {
            $(_this.getPvzButtonJExpr()).trigger("click", {forcy: true});
        }
    };
    
    _this.onChangeRecCity=function(e) {
        if(window.cdek_widgets_DeliveryField.isPvzMode()) {
            $(_this.getPvzButtonJExpr()).trigger("click", {forcy: true});
        }
    };
    
    _this.getPvzData=function() {
        var recCityId=jm("rec_city_id").val();
        var cached=v(_this.cache, recCityId);
        if(cached && is(cached)) {
            return _this.cache[recCityId];
        }
        $.ajax({
            url: o("urlGetPvzList"), 
            async: false,
            data: {cdek_id: recCityId}, 
            dataType: "json",
            success: function(response) {
                _this.cache[recCityId]={};
                if(response.success) {
                    if(is(response.data.pvz)) {
                        _this.cache[recCityId]={};
                        for(var pvzCode in response.data.pvz) {
                            _this.cache[recCityId][pvzCode]=response.data.pvz[pvzCode];
                        };
                    }
                }
            }
        });
        
        if(v(_this.cache, recCityId)) {
            return _this.cache[recCityId];
        }
        
        return {};
    };
    
    /**
     * Открытие окна карты выбора ПВЗ
     */
    _this.openPvzList=function(e, extraParams) {
        e.preventDefault();
        if(v(extraParams, "forcy", false) !== true) {
            if(_this.getPvzCode() && $(o("jPvzContent")).is(":visible")) {
                $(o("jPvzContent")).hide();
                return false;
            }
        }
        
        if(is(_this.getPvzData())) {
            var pvzData=_this.getPvzData();
            $("#cdek_models_Order_pvz_code_em_").hide();
            _this.ymapsInit(pvzData);
            $(o("jPvzContent")).show();
        }
        else {
            jm("pvz_code").val("");
            j("cdek-pvz-info").text("");
            $("#cdek_models_Order_pvz_code_em_").text("Нет доступных ПВЗ. Рекомендуется выбрать ближайший крупный населенный пункт.");
            $("#cdek_models_Order_pvz_code_em_").show();
            $(o("jPvzContent")).hide();
        }
        return false;
    };
    
    _this.ymapsInit=function(pvzData) {
        if(_this.map === null) {
            ymaps.ready(init);
            function init() {
                _this.map=new ymaps.Map(o("jPvzMap").replace(/^#/, ''), {
                    center: [55.76, 37.64],
                    zoom: 17,
                },{});

                //_this.map.behaviors.disable('scrollZoom');

                _this.map.controls
                    //.remove('zoomControl')
                    .remove('trafficControl')
                    .remove('searchControl')
                    .remove('typeSelector')
                    .remove('scaleLine')
                    .remove('mapTools')
                    .remove('mapTools')
                    .remove('miniMap')
                    .remove('searchControl');
                
                /*_this.map=new ymaps.Map(o("jPvzMap").replace(/^#/, ''), {
                    center: [55.029030, 82.926474], 
                    zoom: 17, //o("zoom"), 
                    scrollZoom: false, controls: ["zoomControl"]
                });*/
                
                _this.ymapsAddPoints(pvzData);
            }
        }
        else {
            _this.ymapsAddPoints(pvzData);
        }
    };
    
    _this.ymapsAddPoints=function(pvzData) {
        _this.map.geoObjects.removeAll();
        var mapCollection = new ymaps.GeoObjectCollection();                
        for(var pvzCode in pvzData) {
            var pvz=pvzData[pvzCode];
            var placemarkPreset="islands#blueDeliveryIcon";
            if(_this.getPvzCode() == pvz.Code) placemarkPreset="islands#greenDeliveryIcon";
            var placemark = new ymaps.Placemark([parseFloat(pvz.coordY)+0.0001, parseFloat(pvz.coordX)-0.00005], { 
                hintContent: pvz.Name, 
                balloonContentHeader: pvz.Name,
                balloonContent: _this.ymapsGetPointContent(pvz),
                balloonContentFooter: _this.ymapsGetPointFooter(pvz)
            }, {preset: placemarkPreset});
            mapCollection.add(placemark);
        }
        
        _this.map.geoObjects.add(mapCollection);
        _this.map.setBounds(mapCollection.getBounds(), {checkZoomRange:true, zoomMargin:[50,50,50,50]});
    };
    
    _this.ymapsGetPointContent=function(pvz) {
        var content='';
        var props={
            "Адрес": "Address",
            "Телефон": "Phone",
            "Время работы": "WorkTime"
        }
        for(var label in props) {
            content += "<b>"+label+":</b> " + pvz[props[label]] + "<br/>";
        }
        return content;
    };
    
    _this.ymapsGetPointFooter=function(pvz) {
        if(_this.getPvzCode()==pvz.Code) {
            var content='<span class="cdek-pvz-btn-active">выбрано</span>';
        }
        else {
            var content='<a href="javascript:;" class="cdek-pvz-btn" onclick="window.cdek_widgets_PvzField.setPvzCode(\''+pvz.Code+'\')">выбрать</a>';
        }
        if(pvz.Site) {
            content += '<a href="'+pvz.Site+'" target="_blank" style="float:left">перейти на сайт</a>';
        }
        return content;
    };
    
    _this.updatePvzInfo=function() {
        var pvzData=_this.getPvzData();
        var recCityId=jm("rec_city_id").val();
        
        j("cdek-pvz-info").text("");
        j("cdek-pvz-btn-open").text("выбрать");
        var pvz=v(pvzData, _this.getPvzCode());
        if(pvz) {
            j("cdek-pvz-info").text(" ("+pvz.Name+", "+pvz.Address+")");
            j("cdek-pvz-btn-open").text("изменить");
            $(o("jPvzContent")).hide();
            window.cdek_widgets_DeliveryField.calc();
        }
    };
    
    _this.setPvzCode=function(pvzCode) {
        jm("pvz_code").val(pvzCode);
        _this.map.balloon.close();
        _this.updatePvzInfo();
    };
    
    _this.getPvzCode=function() {
        return jm("pvz_code").val();
    }
        
    return _this;
})();
