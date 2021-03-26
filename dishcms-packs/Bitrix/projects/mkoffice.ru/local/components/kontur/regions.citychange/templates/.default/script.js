jQuery(document).ready(function(){
    var $=jQuery;
    
    window.konturRegionsChangeCity=(function(options){
        var _this={
            initalized: false,
            map: null,
            options: {}
        };
        
        function s(name){return '.js-changecity-' + name;}
        function j(name){return $(s(name));}
        function o(name, def){
            return (typeof _this.options[name] != "undefined")
                ? _this.options[name]
                : ((typeof(def) == 'undefined') ? null : def)
        }
        function cset(citycode){$.cookie(o('cookie'),citycode,{path:'/'});}
        function cget(){return $.cookie(o('cookie'));}
        
        /**
         * @param object options
         * yid: 'идентификатор контейнера яндекс.карты',
         * yzoom: 16,
         * ycenter: [55.029030, 82.926474],
         * cookie: 'имя cookie переменной текущего города'
         * geolocation: false отключает определение местоположения средствами яндекса
         */
        _this.init=function(options) {
            if(!_this.initalized) {
                _this.options=options;
                _this.yinit();
                $(document).on('click', s('btn'), _this.onClickPopupBtn);
                $(document).on('hover', s('region'), _this.onHoverRegion);
                $(document).on('hover', s('city'), _this.onHoverCity);
                $(document).on('click', s('city'), _this.onClickCity);
                $(document).on('mouseleave', s('popup'), _this.onMouseLeavePopup);
                _this.initalized=true;
            }
        };
        
        _this.onMouseLeavePopup=function(e) {
            j('popup').hide();
        };
        
        _this.onClickPopupBtn=function(e) {
            j('popup').show();
            
            e.preventDefault();
            return false;
        };
        
        _this.onHoverRegion=function(e) {
            j('cities').hide();
            $(e.target).closest(s('region')).siblings(s('cities')).show();
        };
        
        _this.onHoverCity=function(e) {
            var $city=$(e.target).closest(s('city'));
            
            j('city').removeClass('active');
            $city.addClass('active');
            
            if($city.data('map').indexOf(',') > -1) {
                _this.ypoint($city.data('map').split(','));
                _this.ymap().show();
            }
            else {
                _this.ymap().hide();
            }
            
            j('region').removeClass('active');
            $city.parents(s('cities')).siblings(s('region')).addClass('active');
            
            j('info-address').text($city.data('address'));            
            j('info').show();
        };
        
        _this.onClickCity=function(e) {
            var code=$(e.target).closest(s('city')).data('code');
            cset(code);
            
            window.location.href=window.location.pathname + '?city=' + code;
            
            e.preventDefault();
            return false;
        }
        
        _this.yinit=function() {
            ymaps.ready(yinit);
            function yinit() {
                _this.map=new ymaps.Map(o('yid'), {center:o('ycenter',[55.029030, 82.926474]), zoom:o('yzoom', 16), scrollZoom:false, controls:["zoomControl"]});
                
                if(!j('city.active').length) {
                    if(cget()) j('city').each(function(){if($(this).data('code')==cget())$(this).addClass('active');});
                    else j('city').eq(0).addClass('active');
                }
                
                j('city.active').trigger('mouseenter');
                
                _this.geolocation();
            }
        };
        
        _this.ymap=function() {
            return $('#'+o('yid'));
        };
        
        _this._ypoint={};
        _this.ypoint=function(coords) {
            if(typeof _this._ypoint[coords.join(',')] == 'undefined') {
                var placemark=new ymaps.Placemark(coords);
                _this.map.geoObjects.add(placemark);
            }
            else {
                _this._ypoint[coords.join(',')]=1;
            }
            _this.map.setCenter(coords);  
        };
        
        _this.geolocation=function() {
            if(!cget() && o('geolocation', true)) {
                ymaps.geolocation.get({
                    provider: 'yandex',
                    mapStateAutoApply: true
                }).then(function(r) {
                    try {
                        var city=r.geoObjects.get(0).properties.get('name').toLocaleLowerCase();
                        j('city').each(function(){
                            if($(this).text().toLocaleLowerCase() === city) {
                                if(!$(this).hasClass('active')) {
                                    $(this).trigger('click');
                                }
                            }
                        });
                    }
                    catch(e) { console.log('GeoLocation: ' + e.getMessage()); }                
                });
            }
        };
        
        return _this;
    })();
});
