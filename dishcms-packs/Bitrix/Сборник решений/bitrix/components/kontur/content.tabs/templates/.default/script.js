$(document).ready(function() {
	$(document).on("click", ".tabs-box .tabs .tabs_item a", function(e) {
		e.preventDefault();
		var $tab=$(e.target).parents(".tabs_item:first");
		if(!$tab.hasClass("active")) {
			var $tabsBox=$(e.target).parents(".tabs-box:first");
			$tabsBox.find(".tabs .tabs_item").removeClass("active");
			$tab.addClass("active");
			var $contItems=$tabsBox.find(".tabs__cont .tabs__cont_item");
			$contItems.removeClass("active");
			$contItems.eq($tab.index()).addClass("active");
		}
		return false;
	});
});
