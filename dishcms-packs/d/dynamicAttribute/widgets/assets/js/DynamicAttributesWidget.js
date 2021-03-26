/**
 * Dynamic Attributes Widget class
 */
var DAW = DynamicAttributesWidget = {
	_data: [],
	
	/**
	 * Initialization.
	 * @param string attribute имя аттрибута
	 * @param string area селектор области, в которой находится содержимое виджета
	 * по умолчанию ".daw-wrapper[data-item='<attribute>']"
	 */
	init: function(attribute, area) {
		if(typeof(area) == 'undefined') 
			area = ".daw-wrapper[data-item='" + attribute + "']";
		
		this._data[attribute] = {
			index: $(area).find(".daw-table tbody tr").length, 
			area: $(area),
		}
		
		DAW.bind($(area));
	},
	
	/**
	 * Bind action events.
	 * @param jQuery $parent родетельский элемент, относительно которого 
	 * происходит поиск элементов. 
	 */
	bind: function($parent) {
		$parent.find(".daw-btn-remove").on("click", function(e) { e.preventDefault(); DAW.remove(e) });
		$parent.find(".daw-btn-add").on("click", function(e) { e.preventDefault(); DAW.add(e) });
	},
	
	/**
	 * Добавление нового элемента
	 * @param event e 
	 */
	add: function(e) {
		var data = DAW._data[$(e.target).data('attribute')];
		var template = data.area.find(".daw-template tbody").html();
		var $tbody = data.area.find(".daw-table tbody");
		
		var $tr = $tbody.append(template.replace(/{{daw-index}}/g, data.index));
		$tr.find("input").attr("disabled", false);
		data.index++;
		
		DAW.bind($tr);
		
		if($tbody.filter(":hidden").size()) {
			$tbody.siblings("thead").show();
			$tbody.show();
		}
	},
	
	/**
	 * Удаление
	 */
	remove: function(e) {
		var $tr = $(e.target).parents("tr:first");
		var $table = $tr.parents("table:first"); 
		
		if($tr.prop("rowIndex") < 0) return;
		
		$tr.remove();
		
		if($table.find("tbody tr").size() < 1) {
			$table.find("thead, tbody").hide();
		}
	}	
}