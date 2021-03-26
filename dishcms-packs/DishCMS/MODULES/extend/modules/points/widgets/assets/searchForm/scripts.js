/**
 * Скрипт для виджета \extend\modules\points\widgets\SearchForm
 *
 * Доступные параметры (опции):
 * - "geoLocationZoom" (integer) масштаб при переходе 
 * в точку "Мое местоположение". По умолчанию 16.
 * 
 * - "gotoGeoLocationOnAfterInit" (boolean) переходить после 
 * инициализации в точку "Мое местоположение" или нет.
 * 
 * - "gotoZoom" (integer) масштаб при переходе по кнопке 
 * перехода к точке указанного адреса. По умолчанию 12.
 * 
 * Для отображения результата, может быть передан параметр 
 * - "searchListView" (string) идентификатор виджета \CListView
 * 
 * либо два обязательных параметра
 * - "searchUrl" (string) URL для Ajax запроса, получения HTML кода 
 * отображения результата поиска "точек продаж".
 * 
 * - "searchContainer" (string) jQuery выражение выборки 
 * контейнера результатов
 * 
 * и дополнительный параметр
 * - "onAfterSearch" (callable) обработчик, который будет вызван 
 * для обработки результата. Первым аргументом будет передан результат
 * поиска.
 * 
 * 
 * - "searchVar" (string) имя переменной для запроса получения
 * HTML кода отображения результата поиска "точек продаж", 
 * в котором будет передана строка запроса. По умолчанию "q".
 * 
 * - "onAfterSearchOutput" (callable) обработчик, который будет вызван 
 * после отображения результата
 * 
 * - "onAfterClickGoto" (callable) обработчик, который будет вызван, 
 * после выполнения события onClickGoto
 */
window.extendPointsWidgetsSearchForm=(function(){
	var _this={
		options: {
			gotoGeoLocationOnAfterInit: true
		},
		map: null
	};
	
	/** 
	 * Получить значение опции
	 */
	function o(name, def, _options) {
		if(typeof(_options) == 'undefined') _options=_this.options;
		if((typeof(_options) == 'object') && (typeof(_options[name]) != 'undefined')) {
			return _options[name];
		}
		return (typeof(def) == 'undefined') ? false : def;
	};
	
	/**
	 * Получить jQuery выражение выборки объекта по "js-" классу 
	 */
	function js(name) {
		return '.js-' + _this.map.container.getParentElement().id + '-' + name;
	}
	
	/**
	 * Получить jQuery объект по "js-" классу 
	 */
	function jjs(name) {
		return $(js(name));
	}
	
	_this.sf={
		get: function(name, returnSelector) {
			return ((typeof returnSelector != 'undefined') && (returnSelector === true)) ? js(name) : jjs(name);
		},
		input: function(returnSelector) {
			return _this.sf.get('points-search-input', returnSelector);
		},		
		submit: function(returnSelector) {
			return _this.sf.get('points-search-submit', returnSelector);
		},		
		result: function(returnSelector) {
			return _this.sf.get('points-search-result', returnSelector);
		},		
		goto: function(returnSelector) {
			return _this.sf.get('points-search-goto', returnSelector);
		}
	}

	/**
	 * Установить объект карты
	 */
	_this.setMap=function(map) {
		_this.map=map;
	};
	
	/**
	 * Установить опции
	 */
	_this.setOptions=function(options) {
		_this.options=options;
	};
	
	/**
	 * Определить геолокацию пользователя и центрировать на ней карту
	 * @param integer zoom масштаб карты при центрировании. По умолчанию 16.
	 */
	_this.geoLocationPlacemark=null;
	_this.gotoGeoLocation=function(zoom) {
		ymaps.geolocation.get({
			provider: 'yandex',
			mapStateAutoApply: true
		}).then(function(r) {
			var coordinates=r.geoObjects.get(0).geometry.getCoordinates();
			
			// @FIXME хардкод связка
			window.extendPointsInit.currentCenter=coordinates;
			
			if(_this.geoLocationPlacemark === null) {
				_this.geoLocationPlacemark=new ymaps.Placemark(coordinates, {
					iconContent: '<strong>Вы здесь</strong>'
		        }, {
		            preset: 'islands#nightStretchyIcon',
		        });
				_this.map.geoObjects.add(_this.geoLocationPlacemark);
			}
            _this.map.setCenter(coordinates, o('geoLocationZoom', o('zoom', 16, {zoom: zoom})));
        });
	};
	
	/**
	 * Поиск
	 */
	_this.search={
		/**
		 * Поиск адреса
		 * @param string q запрос
		 */
		q: function(q) {
			var search=new ymaps.control.SearchControl({options:{noPlacemark:true}});		
			search.search(q).then(function(r) {
				_this.sf.result().html('');
				r.geoObjects.each(function(geo) {
					_this.search.add(geo);
				});
				_this.sf.result().show();
			});
		},
		
		add: function(geo) {
			var li=$('<li></li>');
			li.text(geo.properties.get('text'));
			li.attr('data-lat', geo.geometry.getCoordinates()[0]);
			li.attr('data-lon', geo.geometry.getCoordinates()[1]);
			_this.sf.result().append(li);
		}
	};
	
	_this.onKeyUpInput=function(e) {
		e.preventDefault();
		_this.search.q(_this.sf.input().val());
		return false;
	}
	
	_this.onClickResultItem=function(e) {
		var li=$(e.target).closest('li');
		_this.sf.input().val(li.text());
		_this.sf.input().attr('data-lat', li.attr('data-lat'));
		_this.sf.input().attr('data-lon', li.attr('data-lon')); 
		_this.sf.result().hide();
	};
	
	_this.onClickGoto=function(e) {
		e.preventDefault();
		
		_this.sf.result().hide();		
		
		/* if(_this.sf.input().attr('data-lat') && _this.sf.input().attr('data-lon')) {
			_this.map.setCenter(
				[_this.sf.input().attr('data-lat'), _this.sf.input().attr('data-lon')], 
				o('gotoZoom', 12)
			);
		}
		else {
		*/
			_this.gotoGeoLocation();
		// }
		
		o('onAfterClickGoto', function() {})();
		
		return false;
	};

	_this.onMouseLeaveResult=function(e) {
		_this.sf.result().hide();
	};
	
	_this.onClickSubmit=function(e) {
		e.preventDefault();
		
		_this.sf.result().hide();
		
		let data={_sv: o('searchVar', 'q')};
		data[o('searchVar', 'q')]=_this.sf.input().val();
		
		if(o('searchListView')) {
			$.fn.yiiListView.update(o('searchListView'), {data: data});
			o('onAfterSearchOutput', function(){})();
		}
		else if(o('searchUrl') && o('searchContainer')) {
			$.post(o('searchUrl'), data, function(r) {
				o('onAfterSearch', function(r) {
					$(o('searchContainer')).html(r);
					o('onAfterSearchOutput', function(){})();
				})(r);
			});
		}
		return false;
	};
	
	/**
	 * Обработчик, который может быть вызван после инициализации карты.
	 * Может быть использован для интеграции с виджетом \common\ext\ymap\widgets\YMap
	 * @param object map объект Яндекс.Карты 
	 */
	_this.onAfterInitMap=function(map) {
		_this.setMap(map);
		_this.initEvents();
		
		if(o('gotoGeoLocationOnAfterInit', true)) {
        	_this.gotoGeoLocation();
        }
	};
	
	/**
	 * Инициализация событий элементов формы
	 */
	_this.initEvents=function() {
		$(document).on('keyup', _this.sf.input(true), _this.onKeyUpInput);
		$(document).on('click', _this.sf.result(true) + ' li', _this.onClickResultItem);
		$(document).on('click', _this.sf.goto(true), _this.onClickGoto);
		$(document).on('click', _this.sf.submit(true), _this.onClickSubmit);
		$(document).on('mouseleave', _this.sf.result(true), _this.onMouseLeaveResult);
	};
	
	/**
	 * Инициализация
	 */
	_this.init=function(options) {
		_this.setOptions(options);
	};
	
	return _this;
})();
