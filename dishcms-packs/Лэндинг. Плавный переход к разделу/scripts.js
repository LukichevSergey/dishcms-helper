$(document).ready(function() {
	// @hook for only ie
	if(($.browser.mozilla === true) && (+$.browser.version <= 11)) {
		$("head").append("<style>.mfp-container { left: -120px; } .mfp-content { position:absolute;top:0;bottom:0; }</style>");
	}
	// @hook for only mozilla
	if(($.browser.mozilla === true) && (+$.browser.version > 20)) {
		$("#hypothec").css("display", "table");
	}
	// @hook for only safary 
	if($.browser.safari === true) {
		// for index page 
		$("#our-advantage li").each(function() {
			$(this).css("height", "70px");
			$(this).css("width", "425px");
		});
	}
	// normalize content area height
	var $sectionContent = $('section.content');
	if($sectionContent.length) {
		var 
			docHeight = $(document).height(),
			footerTop = $("footer").offset().top,
			footerHeight = $("footer").height(),
			headerHeight = $("header").height();
		if(footerTop + footerHeight < docHeight) {
			$sectionContent.css('height', (docHeight - footerHeight - headerHeight - 45) + 'px');
		}
	}
	
	/**
	 * @private
	 * Scroll to
	 * @var function 
	 * @param object target jQuery object of target
	 */
	var _funcScrollTo = function($target) {
		// shift for builders block.
		var h = ($target.attr("id") == 'builders') ? 100 : 0;

		$("html, body").stop().animate({
			scrollTop: $target.offset().top - (($("header").offset().top > 0) ? 1 : 2) * $("header").height() - h
		}, 800);
	};
	
	var $section = $(location.hash);
	if($section.length) {
		_funcScrollTo($section); 
		window.setTimeout(function() { _funcScrollTo($section) }, 1000); 
	}
	
	// @see http://bortvlad.ru/jquery/plavnaya-prokrutka-yakor-jquery/
	$("a[href*=#]").bind("click", function(e) {
		var href = $(this).attr("href");
		var hash = href.substr(href.indexOf("#"));
		_funcScrollTo($(hash));
		e.preventDefault();
	});
	
	// scroll top
    $(window).scroll(function() {
	    if($(this).scrollTop() != 0) {
			$("header").addClass("panel");
	    } else {
			$("header").removeClass("panel");
	    }
	});
    
	// flex slider
	/**
	 * @private
	 * Initialize flex slider
	 * @var function 
	 * @param object target
	 * @param string namespace flex slider namespace.
	 */
	var _funcFlexSlideInit = function (target, namespace) {
		namespace = "flex-" + ((namespace == "undefined") ? "" : namespace + "-");
		$(target).flexslider({
			namespace: namespace,
			animation: "slide",
			controlNav: false,
			// itemWidth: 491,
			animationLoop: false,
			slideshow: false,
			prevText: "",           
			nextText: "", 
		});
	};
	
	// buildings slider
	var $firstSlider = $('#buildings .flexslider:first');
	_funcFlexSlideInit($firstSlider, $firstSlider.data("item"));

	// section "buildings"
	var $buildingDataList = $("#buildings .data-list");
	var $buildingDataInfo = $("#buildings .data-info");
	$buildingDataList.find(".items td").on("click", function() {
		var dataItem = $(this).data("item");
		
		$buildingDataInfo.filter(":visible").hide();
		$buildingDataInfo.filter("[data-item=" + dataItem + "]").show();
		
		$buildingDataList.find(".slider:visible").hide();
		$flexslider = $buildingDataList.find(".slider[data-item=" + dataItem + "]");
		$flexslider.show();
		_funcFlexSlideInit($flexslider, dataItem);
	});
	
	// section "builders"
	$('#builders .flexslider').flexslider({
		animation: "slide",
		controlNav: false,
		animationLoop: false,
		itemWidth: 210,
		itemMargin: 4,
		prevText: "",           
		nextText: "", 
	});
	
	// magnific popup
	$(".open-popup-link").magnificPopup({
	  type: "inline",
	  midClick: true // allow opening popup on middle mouse click. Always set it to true if you don't provide alternative source.
	});
	
});
