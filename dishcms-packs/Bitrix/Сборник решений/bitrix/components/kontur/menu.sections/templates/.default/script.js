$(document).ready(function() {
	var isOver=false;
	$(document).on("click", ".js-menu-catalog-popup", function(e) {
		if(isOver || $(".js-menu-catalog").is(":hidden")) {
			isOver=false;
			$(".js-menu-catalog").show();
			e.preventDefault();
			return false;
		}
	});
    $(document).on("mouseover", ".js-menu-catalog-popup", function(e) {
		isOver=true;
        $(".js-menu-catalog").show();
    });
    $(document).on("mouseleave", ".js-menu-catalog", function(e) {
        if(!$(e.target).parents().is(".js-menu-catalog, .js-menu-catalog-popup"))
            $(".js-menu-catalog").hide();
    });
});
