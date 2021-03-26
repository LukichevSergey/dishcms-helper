
$(document).ready(function() {
	$('ul.shop-menu ul li').each(function(i) {
		var a = $(this).children("a");
		var ahref = a.prop("href");
		var idx = ahref.lastIndexOf('/');
		var catId = (idx > 0) ? parseInt(ahref.substr(idx + 1)) : -1;
		$(this).attr("category_id", catId);
		a.attr("class", "noactive");//(catId == _globalCategoryId) ? "active" : "noactive");
		a.on("mouseover", function() { $(this).attr("class", "active"); });
		a.on("mouseout", function() { 
			//if(catId != _globalCategoryId)
				$(this).attr("class", "noactive"); 
		});
	});
	$('ul.shop-menu ul').each(function(i) { // Check each submenu:		
		var li = $(this).parent();
		var isCurrentCat = $(this).parents(".active").length;//false;//(parseInt(li.attr("category_id")) == _globalCategoryId);
		
		if ($.cookie('submenuMark-' + i) || isCurrentCat) {  // If index of submenu is marked in cookies:
			$(this).show().prev().removeClass('collapsed').addClass('expanded'); // Show it (add apropriate classes)
		}else {
			$(this).hide().prev().removeClass('expanded').addClass('collapsed'); // Hide it
		}
		if(isCurrentCat) {
			$(this).parents("ul").show();
		}
		
		$(this).prev().addClass('collapsible').click(function() { // Attach an event listener
			$(this).parent().siblings('li').find('a.expanded').trigger('click');
		
			var this_i = $('ul.shop-menu ul').index($(this).next()); // The index of the submenu of the clicked link
			if ($(this).next().css('display') == 'none') {
				$(this).next().slideDown(200, function () { // Show submenu:
					$(this).prev().removeClass('collapsed').addClass('expanded');
					cookieSet(this_i);
				});
			}else {
				$(this).next().slideUp(200, function () { // Hide submenu:
					$(this).prev().removeClass('expanded').addClass('collapsed');
					cookieDel(this_i);
					$(this).find('ul').each(function() {
						$(this).hide(0, cookieDel($('ul.shop-menu ul').index($(this)))).prev().removeClass('expanded').addClass('collapsed');
					});
				});
			}
			//if(!$(this).hasClass("expanded")) {
				window.location = $(this).prop("href");
			//}
			return false; // Prohibit the browser to follow the link address
		});
		//var currentCat = $("li[category_id=" + _globalCategoryId + "]");
		//currentCat.children("ul").show();
		//currentCat.parents("ul").show();
	});
});
function cookieSet(index) {
	$.cookie('submenuMark-' + index, 'opened', {expires: null, path: '/'}); // Set mark to cookie (submenu is shown):
}
function cookieDel(index) {
	$.cookie('submenuMark-' + index, null, {expires: null, path: '/'}); // Delete mark from cookie (submenu is hidden):
}
