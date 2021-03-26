$(document).ready(function() {
	$(document).on("click", ".kontur_menu_section_list_ext > li > ul > li", function(e) {
		if(!$(e.target).is("a") && $(e.target).find(">a").length) {
			window.location.href=$(e.target).find(">a").attr("href");
		}
	});
});
