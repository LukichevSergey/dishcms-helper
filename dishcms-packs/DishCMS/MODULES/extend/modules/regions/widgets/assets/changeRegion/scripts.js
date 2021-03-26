/**
 * Скрипты для виджета \extend\modules\regions\widgets\ChangeRegion 
 */
var extend_modules_regions_widgets_ChangeRegion=function(name, cookieName) {
	$(document).on("change", ".js-region__changebox[name='"+name+"']", function(e) {
		// $.cookie(cookieName, $(e.target).val(), 3600, "/");
		var url=eval("window.location.href.replace(/[?&]"+name+"=\\d+/g, '')");
		window.location.href=url + ((url.indexOf('?') >= 0) ? '&' : '?') + name + '=' + $(e.target).val(); 
	});
};