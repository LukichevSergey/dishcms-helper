/**
 * DOrderListWidget class 
 * for ListWidget action of DOrder module.
 */
var DOrderListWidget = {
	/**
	 * Изменение статуса заказа "Обработан"
	 */
	actionCompleted: function(e) {
		t = $(this);
        $.ajax({
			type: "POST",
			url: "/cp/dOrder/completed",
			data: {item: $(this).data('item')},
			dataType: "json",
			success: function(json) {
				if(json.success) {
					if(!json.data.status) {
						$(t).removeClass('unmarked');
					} else {
						$(t).addClass('unmarked');
					}
					$('.dorder-order-button-widget-count').text(json.data.count);
				}
			}
        });
	},
	
	/**
	 * Изменение статуса заказа "Оплачен"
	 */
	actionPaid: function(e) {
		t = $(this);
	    $.ajax({
			type: "POST",
			url: "/cp/dOrder/paid",
			data: {item: $(this).data('item')},
			dataType: "json",
			success: function(json) {
				if(json.success) {
					if(json.data.status) {
						$(t).parents("tr[data-item='" + $(t).data('item') + "']").addClass("payment_complete");
						$(t).addClass('unmarked_green');
					} else {
						$(t).parents("tr[data-item='" + $(t).data('item') + "']").removeClass("payment_complete");
						$(t).removeClass('unmarked_green');
					}
				}
			}
	    });
	},
	
	/**
	 * Действие сохранения комментария
	 */
	actionComment: function(e) {
		t = $(this);
        $.ajax({
			type: "POST",
			url: "/cp/dOrder/comment",
			data: {item: $(this).data('item'), comment: t.val()},
			dataType: "json",
			success: function(json) {
				if(json.success) {
					t.val(json.data.comment);
				}
			}
        });
	},
	
	/**
	 * Действие удаление заказа
	 */
	actionDelete: function (e) {
		if(confirm("Подтвердите удаление заказа")) {
			$t = $(this);
	        $.ajax({
				type: "POST",
				url: "/cp/dOrder/delete",
				data: {item: $t.data('item')},
				dataType: "json",
				success: function(json) {
					if(json.success) {
						$(".dorder-admin-list .dorder-list-item[data-item=" + $t.data('item') + "]").remove();
						$(".dorder-admin-list .dorder-list-item-details[data-item=" + $t.data('item') + "]").remove();
						$(".dorder-admin-list .dorder-list-item-comment[data-item=" + $t.data('item') + "]").remove();
						$('.dorder-order-button-widget-count').text(json.data.count);
					}
				}
	        });
		}
	}
}