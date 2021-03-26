Сделан пример, завязан на изменение свойства псевдоэлемента opacity.
document.addEventListener("DOMContentLoaded",function(){
	$(document).on("hover", ".bx-soa-pp-company .bx-soa-pp-company-smalltitle", function(e) {
		//var opacity=window.getComputedStyle(document.getElementsByClassName('bx-soa-pp-company-smalltitle')[0], '::after').getPropertyValue('opacity');
		var opacity=window.getComputedStyle($(e.target).get(0), '::after').getPropertyValue('opacity');
		var $desc=$(e.target).parents(".bx-soa-pp-item-container:first").siblings(".bx-soa-pp-desc-container");
		if(+opacity != 1) $desc.show();
	});
	$(document).on("mouseleave", ".bx-soa-pp-desc-container", function(e) {
		$(e.target).closest(".bx-soa-pp-desc-container").hide();
	});
});
