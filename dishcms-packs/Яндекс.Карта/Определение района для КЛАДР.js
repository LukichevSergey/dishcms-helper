document.addEventListener("DOMContentLoaded",function(){
	// определение района по адресу КЛАДР.
	var kladrIntervalId=setInterval(function(){if((typeof(KladrJsObj.map)!="undefined") && KladrJsObj.map){konturKladrInit();clearInterval(kladrIntervalId);}},200);
	function konturKladrInit() {
		window.KonturKladrJsObjMapCoords=[];
		window.KonturKladrJsObjMapLastCoords=false;
		window.KonturKladrJsObjMapLastDistrict=false;
		KladrJsObj.map.geoObjects.events.add(['pixelboundschange'], function(e) {
			var currentCoords=KladrJsObj.map.geoObjects.get(0).geometry.getCoordinates();
			if(KonturKladrJsObjMapLastCoords && (KonturKladrJsObjMapLastCoords.join(',') == currentCoords.join(','))) {
				return true;
			}
			window.KonturKladrJsObjMapLastCoords=currentCoords;
			var districtData=false;
			window.KonturKladrJsObjMapCoords.forEach(function(data){
				if((data.coords[0] == currentCoords[0]) && (data.coords[1] == currentCoords[1])) {
					districtData=data;
				}
			});
	
			// @var function смена района
			function changeDistrict(districtData) {
				// район сменился
				if(window.KonturKladrJsObjMapLastDistrict != districtData.district.code) {
					window.KonturKladrJsObjMapLastDistrict=districtData.district.code;
					console.log(districtData, window.KonturKladrJsObjMapLastDisrict);
				}
				console.log(true);
			}
	
			if(districtData) {
				changeDistrict(districtData);
			}
			else {
				$.get("http://data.esosedi.org/geocode/v1", {lng:"ru", point: currentCoords.join(',')}, function(response) {
					districtData={coords: currentCoords, district:{code: response.target.ll, name: response.names[response.target.ll].name}};
					window.KonturKladrJsObjMapCoords.push(districtData);
					changeDistrict(districtData);
				}, "json");
			}
			console.log(1);
		});
	}
});

///////// ver new
document.addEventListener("DOMContentLoaded",function(){
	window.KonturKladrJsObjMapCoords=[];
	window.KonturKladrJsObjMapLastCoords=false;
	window.KonturKladrJsObjMapLastDistrict=false;
	// определение района по адресу КЛАДР.
	window.konturKladrInit=function(){
		var kladrIntervalId=setInterval(function(){
			if((typeof(KladrJsObj.map)!="undefined") && KladrJsObj.map){
				konturKladrInit();clearInterval(kladrIntervalId);
			}else{
				if($(".nobasemessage").length) KladrJsObj.nobasemessage();
			}
		},200);
	};
	var kladrIntervalInitId=setInterval(function(){
		if((typeof(KladrJsObj)!="undefined")){
			//window.BX.locationSelectors[<?=$arParams['LOCATIONS_JS_CONTROL_GLOBAL_ID']?>].setValueByLocationCode(<?=$arParams['LOCATIONS_DEFAULT_CODE']?>, true);
			window.BX.locationSelectors[<?=$arParams['LOCATIONS_JS_CONTROL_GLOBAL_ID']?>].setValueByLocationCode(<?=$arParams['LOCATIONS_DEFAULT_ID']?>);
			var locItem=window.BX.locationSelectors[<?=$arParams['LOCATIONS_JS_CONTROL_GLOBAL_ID']?>].getCurrentItem();
			if(typeof(locItem.VALUE) != "undefined") {
				$("[name='ORDER_PROP_4']").val(locItem.VALUE);
				submitForm();
			}
			//console.log("window.BX.locationSelectors[<?=$arParams['LOCATIONS_JS_CONTROL_GLOBAL_ID']?>].setValueByLocationCode(<?=$arParams['LOCATIONS_DEFAULT_CODE']?>, true);");
			// window.BX.locationSelectors[300].getNodeByLocationId(3146)
			//KladrJsObj.nobasemessage();
			clearInterval(kladrIntervalInitId);
		}
	}, 200);
	function konturKladrInit() {
		KladrJsObj.map.geoObjects.events.add(['pixelboundschange'], function(e) {
			var currentCoords=KladrJsObj.map.geoObjects.get(0).geometry.getCoordinates();
			if(KonturKladrJsObjMapLastCoords && (KonturKladrJsObjMapLastCoords.join(',') == currentCoords.join(','))) {
				return true;
			}
			window.KonturKladrJsObjMapLastCoords=currentCoords;
			var districtData=false;
			window.KonturKladrJsObjMapCoords.forEach(function(data){
				if((data.coords[0] == currentCoords[0]) && (data.coords[1] == currentCoords[1])) {
					districtData=data;
				}
			});
	
			// @var function смена района
			function changeDistrict(districtData) {
				// район сменился
				if(window.KonturKladrJsObjMapLastDistrict != districtData.district.code) {
					window.KonturKladrJsObjMapLastDistrict=districtData.district.code;
					window.BX.locationSelectors[<?=$arParams['LOCATIONS_JS_CONTROL_GLOBAL_ID']?>].setValueByLocationCode(districtData.district.code);
					var locItem=window.BX.locationSelectors[<?=$arParams['LOCATIONS_JS_CONTROL_GLOBAL_ID']?>].getCurrentItem();
					if(typeof(locItem.VALUE) != "undefined") {
						$("[name='ORDER_PROP_4']").val(locItem.VALUE);
						submitForm();
					}
					/*$(".bx-old-soa-delivery .bx_element [name='DELIVERY_ID']"
						+ "[value!='<?=intval($arParams['DELIVERY_SELFPICKUP_ID'])?>']"
						+ "[value!='<?=intval($arParams['DELIVERY_OUTNSK_ID'])?>']"
						+ ":radio:eq(0):not(:checked)").trigger("click"); */
					//console.log(districtData, window.KonturKladrJsObjMapLastDisrict);					
					//KladrJsObj.nobasemessage();
				}
				console.log(true);
			}
	
			if(districtData) {
				changeDistrict(districtData);
			}
			else {
				$.get("http://data.esosedi.org/geocode/v1", {lng:"ru", point: currentCoords.join(',')}, function(response) {
					districtData={coords: currentCoords, district:{code: response.target.ll, name: response.names[response.target.ll].name}};
					window.KonturKladrJsObjMapCoords.push(districtData);
					changeDistrict(districtData);
				}, "json");
			}
		});		
	}	
	// submitForm();
});

