$(document).ready(function() {
    $(document).on("click, mouseover", ".js-menu-catalog-popup", function(e) {
        $(".js-menu-catalog").toggle();
    });
    $(document).on("mouseleave", ".js-menu-catalog", function(e) {
        if(!$(e.target).closest(".bx-top-nav").length)
            $(".js-menu-catalog").hide();
    });
});
