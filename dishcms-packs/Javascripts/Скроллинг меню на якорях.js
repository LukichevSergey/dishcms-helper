document.addEventListener("DOMContentLoaded",function(){
	if($(location.hash).length) $("html,body").stop().animate({scrollTop: $(location.hash).offset().top},800); 
	$("a[href^='/#']").bind("click",function(e){
		$("html,body").stop().animate({scrollTop: $($(this).attr("href").replace('/','')).offset().top},800);
		e.preventDefault();
		return false;
	});
});

или

document.addEventListener("DOMContentLoaded",function(){
	if($(location.hash).length) $("html,body").stop().animate({scrollTop: $(location.hash).offset().top},800); 
});
$(document).ready(function(){
    
    ...
    
    $('.menu_block ul li a').on("click" , function(e){
        var current = $(this).attr('href');
        if(~current.indexOf("#")) {
            e.preventDefault();
            if(window.location.pathname != '/') {
                window.location.href='/'+current;
            }
        }
        var top = $(current).offset().top;
        $('body, html').animate({scrollTop: top}, 1500);
    });
