/**
 * Script for AddToButtonWidget widget of DCart module
 * 
 * @use DCart.js
 * @use jquery-impromptu.3.2.min.js
 */
$(function() {
    $.prompt.close = function() {
        var $t = $('#'+ $.prompt.currentPrefix);
        var speed = $.prompt.defaults.promptspeed;
        $t.animate({top: 0}, speed, 'swing', function() {
            $('#'+ $.prompt.currentPrefix + 'box').css('display', 0).remove();
        });
    };
});

$(".dcart-add-to-cart-btn").live("click", function(e) {
    e.preventDefault();
    var data = $(this).attr("data-dcart-attributes");
	DCart.add($(this).attr("href"), data, e);
});
