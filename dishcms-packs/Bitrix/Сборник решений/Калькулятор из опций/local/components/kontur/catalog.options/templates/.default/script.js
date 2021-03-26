/**
 * Cкрипт компонента kontur:catalog.options
 * 
 */
$(document).ready(function(){
	window.onOpenFormOptions=function() {
		var options=$(".js-options__submit").attr("data-options");
		var $o=$("form[name='aspro_stroy_form_order_options']").find('textarea[name="OPTIONS"]');
		$o.hide();
		$o.val(options).attr('readonly', 'readonly').attr('title', options);
		$o.siblings(".js-form-options-text").remove();
		$o.after('<div class="js-form-options-text" style="max-height:90px;overflow-y:scroll;overflow-x:hidden;border:1px solid #d8dfe4;border-radius:2px;font-size:0.9em;padding:5px;line-height:1.2em;">' + options.replace(/[\n]/g, "<br/>") + "</div>");		
	};
	function normalize(price) { return isNaN(+price) ? 0 : +price; }
	function set_options() {
		var text="", options="", price=0;
		var baseprice=normalize($(".js-total-price-base").data("price"));
		$(".js-options-input:checked").each(function(){
			options += $(this).parents(".options__row:first").find(".options-group__title").text() + ", ";
			options += $(this).siblings(".options-item__title").text();			
			options += ": " + $(this).siblings(".options-item__price").text() + "\n";
			price+=normalize($(this).data("price"));
		});
		text += "БАЗОВАЯ СТОИМОСТЬ: " + baseprice + " руб.\n";
		text += "СТОИМОСТЬ ОПЦИЙ: " + price + " руб.\n";
		text += "ИТОГОВАЯ ЦЕНА: " + (baseprice + price) + " руб.\n";
		text += options;
		$(".js-options__submit").attr("data-options", text);
	}
	function calc() {
		var price=0;
		$(".js-options-input:checked").each(function(){price+=normalize($(this).data("price"));});
		$(".js-total-price-options").text(price);
		$(".js-total-price").text(normalize($(".js-total-price-base").data("price")) + price);
		set_options();
	}
	$(document).on("change, click", ".js-options-input", calc);
	$(document).on("click", ".js-btn-goto", function(e){
		e.preventDefault();
		$('body, html').animate({scrollTop: $($(e.target).closest("a").attr("href")).offset().top - 50}, 500);
		return false;
	});
	set_options();
});