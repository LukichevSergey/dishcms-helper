var ShopMenuCollapsed = {
 cookieVarName: "shopMenuItemIndex",
 
 init: function(jQExpr) {
  var self = this;

  $(jQExpr + " >li:not(:has(ul))").addClass('noncollapsed');
  $(jQExpr + " .expanded").removeClass("expanded");
  $(jQExpr + " li").click(function(e) { 
  		if(!$(this).filter(".collapsed,.expanded").length) {
  			window.location = $(e.target).attr("href");
  			e.stopPropagation();
  		}
  });

    if($( jQExpr + " .active.noncollapsed" ).length)  {
   		$(jQExpr + " >li").each(function() { 
   			self.close($(this)); 
   		});
   		$.cookie(self.cookieVarName, null, {expires: null, path: '/'});   		
	}

  var openedIndex = $.cookie(self.cookieVarName);
  
  $(jQExpr + " ul").each(function(index) {
   var li = $(this).parent();

   if(index == openedIndex) self.open(li);
   else self.close(li, true);

    $( li ).click(function(e) {
		var isRedirect = $(this).find('a[href$="/1"],[href$="/3"]').length;

    	if($(this).filter(".collapsed").length) { // если закрыто
		     $(jQExpr + " .expanded").each(function() {
     		self.close($(this), true);
     	});

     	if(isRedirect) $.cookie(self.cookieVarName, index, {expires: null, path: '/'});
     	else {
			$(this).children("ul").slideDown(200, function () { // Show submenu:
    	 		self.open(li);
     			$.cookie(self.cookieVarName, index, {expires: null, path: '/'});
     		});
     	}
    }
    else {
    	if(isRedirect) $.cookie(self.cookieVarName, null, {expires: null, path: '/'});
    	else {
	    	$(this).children("ul").slideUp(200, function () { // Hide submenu:
		    	self.close(li);
	    	 	$.cookie(self.cookieVarName, null, {expires: null, path: '/'});
	 		});
	 	}
    }
    if(!isRedirect) e.preventDefault();
   });
  });
 },
 
 open: function($obj) {
  $obj.removeClass("collapsed");
  $obj.addClass("expanded");
 },
 
 close: function($obj, slideUp) {
  	if(slideUp === true) {
  		$obj.children("ul").slideUp(200, function() {
  			$obj.removeClass("expanded");
			$obj.addClass("collapsed");
	  	});
  	} 
	else {
  		$obj.removeClass("expanded");
	  	$obj.addClass("collapsed");
	}
 }


}

$(document).ready(function() {
	ShopMenuCollapsed.init("ul.shop-menu");
});



/*$(document).ready(function() {
	$('ul.shop-menu ul').each(function(i) { // Check each submenu:
		if ($.cookie('submenuMark-' + i)) {  // If index of submenu is marked in cookies:
			$(this).show().prev().removeClass('collapsed').addClass('expanded'); // Show it (add apropriate classes)
		}else {
			$(this).hide().prev().removeClass('expanded').addClass('collapsed'); // Hide it
		}
		$(this).prev().addClass('collapsible').click(function() { // Attach an event listener
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
				
					var allLinks = $(".collapsed");
					allLinks.click(function() {

				    allLinks.not(this).next().slideUp(200, function () { // Hide submenu:
					$(this).prev().removeClass('expanded').addClass('collapsed');
					cookieDel(this_i);
					$(this).find('ul').each(function() {
						$(this).hide(0, cookieDel($('ul.shop-menu ul').index($(this)))).prev().removeClass('expanded').addClass('collapsed');
					});

				});
				});



		return false; // Prohibit the browser to follow the link address
		});
	});
});
function cookieSet(index) {
	$.cookie('submenuMark-' + index, 'opened', {expires: null, path: '/'}); // Set mark to cookie (submenu is shown):
}
function cookieDel(index) {
	$.cookie('submenuMark-' + index, null, {expires: null, path: '/'}); // Delete mark from cookie (submenu is hidden):
}
*/
