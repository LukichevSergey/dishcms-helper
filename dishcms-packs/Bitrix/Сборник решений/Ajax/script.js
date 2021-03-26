// Location change
BX.addCustomEvent('rs.location_change', function(data) {
	$.post('/?ajaxc=changelocation&ajaxa=geturl', {id: data.id}, function(response){
		if(response.changed === true) window.location.href=response.url;
	}, "json");
});


BX.ready(function() {
	BX.onCustomEvent('rs.location_change', [{id: RS.Location.data.ID}]);
});
