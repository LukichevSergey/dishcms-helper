var ShopMenuCollapsed = {
	cookieVarName : "shopMenuItemIndex",

	init : function(jQExpr) {
		var self = this;

		$(jQExpr + " .expanded").removeClass("expanded");
		$(jQExpr + " li").click(function(e) {
			if (!$(this).filter(".collapsed,.expanded").length) {
				window.location = $(e.target).attr("href");
				e.stopPropagation();
			}
		});

		var openedIndex = $.cookie(self.cookieVarName);

		$(jQExpr + " ul").each(function(index) {
			var li = $(this).parent();

			if (index == openedIndex)
				self.open(li);
			else
				self.close(li, true);

			$(li).click(function(e) {
				e.preventDefault();
				if ($(this).filter(".collapsed").length) { // если закрыто
					$(jQExpr + " .expanded").each(function() {
						self.close($(this), true);
					});

					$(this).children("ul").slideDown(200, function() { // Show submenu:
						self.open(li);
						$.cookie(self.cookieVarName, index, {
							expires : null,
							path : '/'
						});
					});
				} else {
					$(this).children("ul").slideUp(200, function() { // Hide submenu:
						self.close(li);
						$.cookie(self.cookieVarName, null, {
							expires : null,
							path : '/'
						});
					});
				}
			});
		});
	},

	open : function($obj) {
		$obj.removeClass("collapsed");
		$obj.addClass("expanded");
	},

	close : function($obj, slideUp) {
		$obj.removeClass("expanded");
		$obj.addClass("collapsed");
		if (slideUp === true)
			$obj.children("ul").slideUp(200);
	}
}

$(document).ready(function() {
	ShopMenuCollapsed.init("ul.shop-menu");
});