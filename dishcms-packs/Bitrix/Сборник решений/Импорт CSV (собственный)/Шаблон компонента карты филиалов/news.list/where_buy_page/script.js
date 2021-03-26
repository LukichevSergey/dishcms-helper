document.addEventListener("DOMContentLoaded",function(){
    window.mapDialers = (function(){
        var _this={};
        
        _this.map = null;
        _this.options = {};
        
        function o(name) {
            try {return _this.options[name];}
            catch(e){return null;}
        }
        
        _this.getDestinations = function(position) {
            if(isNaN(+position)) position = 0;
            if(position) {
                var i=1;
                for(var id in o("destinations")) {
                    if(position==i++) return o("destinations")[id];
                }
                return null;
            }
            return o("destinations");
        };
        
        _this.init = function(options) {
            _this.options = options;
            window.mapDialersInvervalID = setInterval(function(){
                if(typeof(ymaps) != "undefined") {
                    ymaps.ready(_this.initMap);
                    clearInterval(window.mapDialersInvervalID);
                }
            }, 200);
        },
        
        _this.initMap = function() {
            var center = [55.725047, 37.646866]; // Москва
            var destination = {};
            try {
                if(o("center").lat.length && o("center").lon.length) {
                    center = [o("center").lat, o("center").lon];
                }
                /* else if(destination = _this.getDestinations(1)) {
                    center = destination.COORDS;
                } */
            }
            catch(e) {
                /* if(destination = _this.getDestinations(1)) {
                    center = destination.COORDS;
                } */
            }
                
            _this.map = new ymaps.Map(o('mapID'), {
                // При инициализации карты обязательно нужно указать
                // её центр и коэффициент масштабирования.
                center: center,
                zoom: (isNaN(+o("zoom")) ? 10 : +o("zoom")),
                controls: ['smallMapDefaultSet']
			});
            
            _this.addDestinations();
        };
        
        _this.addDestinations = function(setBounds) {
            _this.map.geoObjects.removeAll();
            var mapCollection = new ymaps.GeoObjectCollection();
            for(var id in o("destinations")) {
                var destination=o("destinations")[id];                
                var placemark = new ymaps.Placemark(destination.COORDS, {
                    name: destination.NAME
                }, { 
                    balloonContentLayout: _this.getDestinationBallonLayout(destination),
                    balloonPanelMaxMapArea: 0,
                    /* iconLayout: 'default#image',
                    iconImageHref: o("icon"),
                    iconImageSize: [50, 62],
                    iconImageOffset: [0, 0], */
                });
                mapCollection.add(placemark);
            }
         
            _this.map.geoObjects.add(mapCollection);
            
            if(setBounds === true) {
                _this.map.setBounds(mapCollection.getBounds(), {checkZoomRange:true, zoomMargin:[50,50,50,50]});
            }
        };
        
        _this.getDestinationBallonLayout = function(destination) {
            var template = "";
            
            template += '<div class="baloon">'
            
            template += '<div class="baloon__header">'
            template += '<span class="baloon__title">' + destination.NAME + '</span>';
            
            if(destination.PARTNER_TYPE) { 
                template += '<div class="baloon__header-icons">';
               
                o("partnerType").forEach(function(item){
                    if((Array.isArray(destination.PARTNER_TYPE) && (destination.PARTNER_TYPE.indexOf(item.XML_ID) > -1)) 
                        || (destination.PARTNER_TYPE == item.XML_ID)) 
                    {
                        template += '<div class="baloon__header-icon">';
                        template += '<img src="' + o("icons") + item.ICON +'" alt="' + item.ALT + '" title="' + item.ALT + '">';
                        template += '</div>';
                    }
                });
                
                template += '</div>';
            }
            
            template += '</div>';
            
            template += '<div class="baloon__content">';
            
            if(destination.ADDRESS) {
                template += '<div class="baloon__contact">';
                template += '<div class="baloon__contact-icon">';
                template += '<img src="' + o("icons") + '/icon-26.svg" alt="">';
                template += '</div>';
                template += '<span class="baloon__contact-text">' + destination.ADDRESS + '</span>';
                template += '</div>';
            }
            
            if(destination.EMAIL) {
                template += '<div class="baloon__contact">';
                template += '<div class="baloon__contact-icon">';
                template += '<img src="' + o("icons") + '/icon-28.svg" alt="">';
                template += '</div>';
                template += '<a class="baloon__contact-text" href="mailto:' + destination.EMAIL + '">' + destination.EMAIL + '</a>';
                template += '</div>';
            }
            
            if(destination.PHONE) {
                template += '<div class="baloon__contact">';
                template += '<div class="baloon__contact-icon">';
                template += '<img src="' + o("icons") + '/icon-27.svg" alt="">';
                template += '</div>';
                template += '<a class="baloon__contact-text" href="tel:' + destination.PHONE + '">' + destination.PHONE + '</a>';
                template += '</div>';
            }
            
            if(destination.SITE) {
                template += '<div class="baloon__contact">';
                template += '<div class="baloon__contact-icon">';
                template += '<img src="' + o("icons") + '/icon-29.svg" alt="">';
                template += '</div>';
                template += '<a class="baloon__contact-text" href="' + destination.SITE + '" target="_blank">' + destination.SITE + '</a>';
                template += '</div>';
            }
            
            template += '</div>';
            
            template += '<div class="baloon__footer">';
            template += '<div class="baloon__footer-icons">';
            if(destination.SPECIALIZATION) { 
                o("specialization").forEach(function(item){
                    if((Array.isArray(destination.SPECIALIZATION) && (destination.SPECIALIZATION.indexOf(item.XML_ID) > -1)) 
                        || (destination.SPECIALIZATION == item.XML_ID)) 
                    {
                        template += '<div class="baloon__footer-icon">';
                        template += '<img src="'+ o("icons") + item.ICON +'" alt="' + item.ALT + '" title="' + item.ALT + '">';
                        template += '</div>';
                    }
                });
            }
            template += '</div>';
            template += '</div>';
            
            template += '</div>';
            
            return (function(){
                var BalloonContentLayout = ymaps.templateLayoutFactory.createClass(template, {
                    build: function () {
                        BalloonContentLayout.superclass.build.call(this);
                    },
                    clear: function () {
                        BalloonContentLayout.superclass.clear.call(this);
                    }
                });
                return BalloonContentLayout;
            })();
        };
        
        return _this;
    })();
});
