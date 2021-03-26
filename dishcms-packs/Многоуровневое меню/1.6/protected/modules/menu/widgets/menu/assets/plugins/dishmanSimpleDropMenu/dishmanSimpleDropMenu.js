/**
 * dishmanSimpleDropMenu 
 */

var dishmanSimpleDropMenu = {
	/**
	 * Initialization
	 * @param string id root UL id.  
	 */
	init: function(id) {
		$("nav ul#" + id + " ul").each(function(i) {
			$(this).css("z-index", $(this).closest("ul").css("z-index") + 1);
		});
		$("nav ul#" + id + " li")
			.on("click", function(e) {
				window.location = $(e.target).children("a:first").attr("href");
			})
			.on("mouseover", function() {
				$(this).addClass("active");
				$(this).children("ul").show();
			})
			.on("mouseout", function() {
				$(this).children("ul").hide();
				$(this).removeClass("active");
			}); 
	}
}