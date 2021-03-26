/**
 * Скрипт для виджета \rpochta\widgets\OpsField
 */
window.rpochta_widgets_OpsField=(function(){
    //var defaultRPochtaOpsVariant=11;
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
        $elm=$(".rpochta__box [data-js='"+name+"']");
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
     *  "attribute_index"
     * 
     *  "urlGetOpsList" URL получения списка ОПС
     *  "jCity" выражение выборки элемента с почтовым индексом города для которого генерится карта ОПС.
     *  "jOpsButton" выражение выборки элемента открытия окна выбора ОПС.
     *  "jOpsContent" выражение выборки элемента выбора ОПС.
     *  "jOpsMap" выражение выборки элемента в котором будет отображена карта ОПС.
     */
    _this.init=function(options) {
        _this.options=options;
        $box=$(".rpochta__box");
        
        j(o("jOpsButton")).html($.parseHTML('&nbsp;<span data-js="rpochta-ops-info"></span>&nbsp;<a data-js="rpochta-ops-btn-open" href="javascript:;">выбрать</a>'));
        
        $(document).on("click", _this.getOpsButtonJExpr(), _this.openOpsList);
        
        $box.on("change", "[data-js='mode']", _this.onChangeMode);
        $(document).on("change", m("index_to"), _this.onChangeIndexTo);
        
        _this.updateOpsInfo();
    };
    
    _this.isOpsMode=function() {
        return window.rpochta_widgets_DeliveryField.isOpsMode();
    };
    
    _this.getOpsButtonJExpr=function() {
        return "[data-js='"+o("jOpsButton")+"'] a";
    };
    
    _this.onChangeMode=function(e) {
        if(_this.isOpsMode() && !_this.getOpsAddress()) {
            $(_this.getOpsButtonJExpr()).trigger("click", {forcy: true});
        }
    };
    
    _this.onChangeIndexTo=function(e) {
        jm("ops_index").val("");
        jm("ops_address").val("");
        jm("ops_latitude").val("");
        jm("ops_longitude").val("");
        
        if(_this.isOpsMode()) {
            $(_this.getOpsButtonJExpr()).trigger("click", {forcy: true});
        }
    };
    
    _this.getOpsData=function() {
        var cityIndexTo=jm("index_to").val();
        if(_this.isOpsMode() && jm("ops_index").val()) {
            cityIndexTo=jm("ops_index").val();
        }
        
        var cached=v(_this.cache, cityIndexTo);
        if(cached && is(cached)) {
            return _this.cache[cityIndexTo];
        }
        
        var address=jm("index_to").find(":selected").text();
        if(_this.isOpsMode() && jm("ops_address").val()) {
            address=address + "," + jm("ops_address").val();
        }
        
        if(_this.isOpsMode() && jm("ops_latitude").val() && jm("ops_longitude").val()) {
            var data={
                latitude: jm("ops_latitude").val(), 
                longitude: jm("ops_longitude").val()
            };
        }
        else {
            var data={
                postcode: cityIndexTo,
                address: address
            };
        }
        
        $.ajax({
            url: o("urlGetOpsList"), 
            async: false,
            data: data, 
            dataType: "json",
            success: function(response) {
                _this.cache[cityIndexTo]={};
                if(response.success) {
                    if(is(response.data.ops)) {
                        _this.cache[cityIndexTo]={};
                        for(var postcode in response.data.ops) {
                            _this.cache[cityIndexTo][postcode]=response.data.ops[postcode];
                        };
                    }
                }
            }
        });
        
        if(v(_this.cache, cityIndexTo)) {
            return _this.cache[cityIndexTo];
        }
        
        return {};
    };
    
    /**
     * Открытие окна карты выбора ОПС
     */
    _this.openOpsList=function(e, extraParams) {
        e.preventDefault();
        if(v(extraParams, "forcy", false) !== true) {
            if(_this.getOpsAddress() && $(o("jOpsContent")).is(":visible")) {
                $(o("jOpsContent")).hide();
                return false;
            }
        }
        
        if(is(_this.getOpsData())) {
            var opsData=_this.getOpsData();
            $("#rpochta_models_Order_ops_address_em_").hide();
            _this.ymapsInit(opsData);
            $(o("jOpsContent")).show();
        }
        else {
            jm("ops_address").val("");
            j("rpochta-ops-info").text("");
            $("#rpochta_models_Order_ops_address_em_").text("Нет доступных ОПС. Рекомендуется выбрать ближайший крупный населенный пункт.");
            $("#rpochta_models_Order_ops_address_em_").show();
            $(o("jOpsContent")).hide();
        }
        return false;
    };
    
    _this.getOps=function() {
        var opsData=_this.getOpsData();
        var opsAddress=_this.getOpsAddress();
        for(var idx in opsData) {
            if(opsData[idx]["address-source"] == opsAddress) {
                return opsData[idx];
            }
        }
        return false;
    };
    
    _this.updateOpsInfo=function(ops) {
        if(typeof(ops) == "undefined") {
            ops=_this.getOps();
        }
        if(ops) {
            j("rpochta-ops-info").text("");
            j("rpochta-ops-btn-open").text("выбрать");
            j("rpochta-ops-info").text(" ("+ops["postal-code"]+", "+ops["address-source"]+")");
            j("rpochta-ops-btn-open").text("изменить");
            $(o("jOpsContent")).hide();
            window.rpochta_widgets_DeliveryField.calc();
        }
        else {
            j("rpochta-ops-info").text("");
            j("rpochta-ops-btn-open").text("выбрать");
        }
    };
    
    _this.setOpsAddress=function(opsAddress, opsIndex, opsLatitude, opsLongitude) {
        jm("ops_address").val(opsAddress);
        jm("ops_index").val(opsIndex);
        jm("ops_longitude").val(opsLongitude);
        jm("ops_latitude").val(opsLatitude);
        _this.map.balloon.close();
        _this.updateOpsInfo({"address-source": opsAddress, "postal-code": opsIndex, "longitude":opsLongitude, "latitude":opsLatitude});
    };
    
    _this.getOpsAddress=function() {
        return jm("ops_address").val();
    }
    
    _this.ymapsInit=function(opsData) {
        
        if(_this.map === null) {
            ymaps.ready(init);
            function init() {
                _this.map=new ymaps.Map(o("jOpsMap").replace(/^#/, ''), {
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
                
                // информационный блок
                var NoteControlClass = function (options) {
                    NoteControlClass.superclass.constructor.call(this, options);
                    this._$content = null;
                    this._geocoderDeferred = null;
                };
                
                ymaps.util.augment(NoteControlClass, ymaps.collection.Item, {
                    onAddToMap: function (map) {
                        NoteControlClass.superclass.onAddToMap.call(this, map);
                        this._lastCenter = null;
                        this.getParent().getChildElement(this).then(this._onGetChildElement, this);
                    },

                    onRemoveFromMap: function (oldMap) {
                        this._lastCenter = null;
                        if (this._$content) {
                            this._$content.remove();
                            this._mapEventGroup.removeAll();
                        }
                        NoteControlClass.superclass.onRemoveFromMap.call(this, oldMap);
                    },

                    _onGetChildElement: function (parentDomContainer) {
                        this._$content = $('<div class="note-ops">Нажмите на карту, чтобы получить ближайшие, к выбранному месту, ОПС.</div>').appendTo(parentDomContainer);
                        this._mapEventGroup = this.getMap().events.group();                        
                    }
                });
                
                var noteControl = new NoteControlClass();
                _this.map.controls.add(noteControl, {
                    float: 'none',
                    position: {
                        top: 10,
                        left: 50
                    }
                });
                
                // обработчик клика по карте
                _this.map.events.add('click', _this.ymapsOnClick);
                
                /*_this.map=new ymaps.Map(o("jPvzMap").replace(/^#/, ''), {
                    center: [55.029030, 82.926474], 
                    zoom: 17, //o("zoom"), 
                    scrollZoom: false, controls: ["zoomControl"]
                });*/
                
                _this.ymapsAddPoints(opsData);
            }
        }
        else {
            _this.ymapsAddPoints(opsData);
        }
    };
    
    _this.ymapsOnClick=function(e) {
        var coords = e.get('coords');
        $.ajax({
            url: o("urlGetOpsList"),
            data: {
                latitude: coords[0].toPrecision(6),
                longitude: coords[1].toPrecision(6)
            }, 
            dataType: "json",
            success: function(response) {
                if(response.success) {
                    if(is(response.data.ops)) {
                        _this.ymapsAddPoints(response.data.ops);
                    }
                }
            }
        });
    };
    
    _this.ymapsAddPoints=function(opsData) {
        _this.map.geoObjects.removeAll();
        var mapCollection = new ymaps.GeoObjectCollection();                
        for(var idx in opsData) {
            var ops=opsData[idx];
            var placemarkOptions={preset: "islands#blueDeliveryIcon"};
            if(_this.getOpsAddress() == ops["address-source"]) {
                placemarkOptions={
                    preset: "islands#greenDeliveryIcon",
                    zIndex: 999
                };
            }
            var placemark = new ymaps.Placemark([parseFloat(ops.latitude)+0.0001, parseFloat(ops.longitude)-0.00005], { 
                hintContent: ops["address-source"], 
                balloonContentHeader: ops["address-source"],
                balloonContent: _this.ymapsGetPointContent(ops),
                balloonContentFooter: _this.ymapsGetPointFooter(ops)
            }, placemarkOptions);
            mapCollection.add(placemark);
        }
        
        _this.map.geoObjects.add(mapCollection);
        _this.map.setBounds(mapCollection.getBounds(), {checkZoomRange:true, zoomMargin:[50,50,50,50]});
    };
    
    _this.ymapsGetPointContent=function(ops) {
        var content='';
        
        content='На данный момент: ';
        if(ops["is-closed"]) content+='<b style="color:#c00;">закрыто</b><br/>';
        else content+='<b style="color:#3c763d;">работает</b><br/>';
        
        var props={
            "Почтовый индекс": "postal-code",
            "Тип": "type-code"
        }
        for(var label in props) {
            content += "<b>"+label+":</b> " + ops[props[label]] + "<br/>";
        }
        return content;
    };
    
    _this.ymapsGetPointFooter=function(ops) {
        if(_this.getOpsAddress()==ops["address-source"]) {
            var content='<span class="rpochta-ops-btn-active">выбрано</span>';
        }
        else {
            var content='<a href="javascript:;" class="rpochta-ops-btn"'
                + ' onclick="window.rpochta_widgets_OpsField.setOpsAddress(\''
                + ops["address-source"]+'\',\''
                + ops["postal-code"]+'\',\''
                + ops["latitude"]+'\',\''
                + ops["longitude"]
                +'\')">выбрать</a>';
        }
        
        return content;
    };
      
    return _this;
})();
