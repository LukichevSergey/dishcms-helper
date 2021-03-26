// добавить в конец файла js/prism.min.js
(function(){document.addEventListener("DOMContentLoaded",function(){
$("a[href$='"+window.location.pathname.replace(/^.*?([^/]+)$/g, '$1')+"']")
	.parents(".accordion:not(:last)")
	.find("> .accordion-group > .accordion-heading > .accordion-toggle")
	.trigger("click");
});})();
// или min
(function(){document.addEventListener("DOMContentLoaded",function(){$("a[href$='"+window.location.pathname.replace(/^.*?([^/]+)$/g, '$1')+"']").parents(".accordion:not(:last)").find("> .accordion-group > .accordion-heading > .accordion-toggle").trigger("click");});})();