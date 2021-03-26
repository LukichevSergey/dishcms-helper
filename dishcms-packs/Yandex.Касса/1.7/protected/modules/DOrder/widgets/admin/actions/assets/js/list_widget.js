/**
 * Script for ListWidget of DOrder module.
 */
$(function() {
	$list = $(".dorder-admin-list");
	
	$list.on('click', '.orderuser', function(){
		$("table#orders").find(".details[data-item='" + $(this).data('item') + "']").toggle();
		$("table#orders").find(".order[data-item='" + $(this).data('item') + "']").find('.sumprice').toggleClass('actsum')
    });
    
	$list.on('click', '.mark', DOrderListWidget.actionCompleted);
	$list.on('click', '.mark_green', DOrderListWidget.actionPaid);
	$list.on('click', '.dorder-btn-delete', DOrderListWidget.actionDelete);
	$list.on('blur', '.comment', DOrderListWidget.actionComment);
});