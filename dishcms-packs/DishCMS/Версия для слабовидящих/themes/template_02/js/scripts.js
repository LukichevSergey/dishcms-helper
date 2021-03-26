$(document).ready(function(){

	if($(".menu_block ul .active ul").length) {
		$(".menu_block ul .active ul").addClass("menu clearfix sub_menu");
		$(".menu_block section nav").append("<p class='sub_menu_head'>Внутренние страницы:</p>");
		$(".menu_block ul .active ul").appendTo($(".menu_block section nav"));
	}


	var fontSize = function (size) {
		$("body").css('font-size', size+'px');
		$(".size").removeClass("active");
		$(".size[data-size="+size+"]").addClass("active");
	}

	if($.cookie('font-size')) {
		fontSize($.cookie('font-size'), {expires: null, path: '/'});
	}

	$(".size").click(function() {
		$.cookie('font-size', $(this).data('size'), {expires: null, path: '/'});
		fontSize($(this).data('size'));
	});

	// ==============================================

	var pageColor = function (color) {
		$("body").removeClass();
		$("body").addClass(color)
		$(".color").removeClass("active");
		$(".color[data-color="+color+"]").addClass("active");
	}

	if($.cookie('page-color')) {
		pageColor($.cookie('page-color'), {expires: null, path: '/'});
	}
	else {
		$.cookie('page-color', 'white', {expires: null, path: '/'});
		pageColor("white");
	}

	$(".color").click(function() {
		$.cookie('page-color', $(this).data('color'), {expires: null, path: '/'});
		pageColor($(this).data('color'));
	});

	// прибиваем футер к низу страницы
	if($('.footer_block').length){
		setTimeout(function(){
			var hfb = ($(".footer_block").innerHeight());
			$(".page_wrap").css("margin-bottom", -hfb+"px");
			$("body").append('<style>.page_wrap:after{height: '+hfb+'px}</style>');
		},100);
	}

	//Search SHOW
	if($('#search').length){
		var search = $("#search");
		$( '#search-click' ).click(function(){
			if ( search.hasClass( 'active' ) ) {
				search.hide( 'fast' ).removeClass( 'active' );
			}
			else {
				search.show( 'fast' ).addClass( 'active' );
			}
		});
	}


	$('body').on('click', '.open-popup-link', function(){

      $.magnificPopup.open({
        items: {
          src: $(this).attr('href')
        },
        type:'inline',
        midClick: true
      });

      return false;
  });

	// if($('#slider').length){
	// 	$('#slider').bxSlider({
	// 		auto: true,
	// 		prevSelector: '.slides',
	// 		nextSelector: '.slides',
	// 		minSlides: 1,
	// 		maxSlides: 1,
	// 		moveSlides: 1
	// 	});
	// }

	$(document).on('click', '.to-cart', function(){
		bTop=parseInt($(".cart-btn-open-modal-wrapper").css('top'), 10);
		$(".cart-btn-open-modal-wrapper").animate({'top': bTop+10}, 150).delay(50).animate({'top': bTop}, 200);
		if($(this).children(".incart")) {
			$(this).children(".incart").addClass("jump");
			setTimeout('$(".incart").removeClass("jump")', 300);
		}
		$(this).children("span").addClass("incart");
	})

});

$(function() {
	$(window).scroll(function() {
	if($(this).scrollTop() != 0) {
		$('#totop').fadeIn();
	} else {
		$('#totop').fadeOut();
	}
});

$('#totop').click(function() {
	$('body,html').animate({scrollTop:0},800);
	});
});