/**
 * DListBoxAttribute admin widget
 */
var DListBoxAttributeAdminWidget = {
	/**
	 * Удаление. $.ajax.beforeSend
	 */
	removeBeforeSend: function() {
		return confirm("Подтвердите удаление");
	},
	
	/**
	 * Удаление. $ajax.success
	 */
	removeSuccess: function(json) { 
		if(json.success) $("#d-list-box-attribute-" + json.data.id).remove(); 
		else alert(json.errorDefaultMessage); 
	}
}