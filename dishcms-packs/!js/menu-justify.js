// menu justify
var contentWidth = 1000;
var maxH = 0;
$(".menu-block .menu li").each(function() { maxH += $(this).width(); });
if((contentWidth - maxH) <= 20) {
	var $liLast = $(".menu-block .menu li:last");
	$liLast.css("width", $liLast.width() + (contentWidth - maxH));
}
