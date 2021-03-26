window.searchBoxIntervalID=null;
jQuery(document).ready(function(){
	function showBoxQResults() {
		jQuery(".search_box-qresults").show();
		window.searchBoxIntervalID=setInterval(function(){
			if(!jQuery(".search_box-qresults li:hover").length){
				clearInterval(window.searchBoxIntervalID);
				jQuery(".search_box-qresults").hide();
			}
		}, 3000);
	}
	jQuery(document).on("focus", ".search_box input:text",function(e){
		if(jQuery(".search_box-qresults li").length>0)showBoxQResults();
	});
	jQuery(document).on("keyup", ".search_box input:text", function(e) {
		var q=jQuery(e.target).val();
		if(q.length > 1) {
			url="/ajax/search.php?" + Date.now();
			jQuery.post(url, {q: q}, function(r){
				jQuery(".search_box-qresults").html(r);
				showBoxQResults();
			});
		}
	});
	jQuery(document).on("click", ".search_box-qresults li", function(e) {
		window.location=jQuery(e.target).data("href");
	});
});