/**
 * Script for AciSortableMenuWidget
 * 
 * Use aciSortable jQuery plugin
 * @link http://plugins.jquery.com/aciSortable/
 * 
 * Before using you must call "init" method.
 */
var AciSortableMenuWidget = {
	/**
	 * @var object propery as name of execute method, value as url for ajax request.
	 */
	urls: {save: "/menu/aciSortableAdmin/save"},

	/**
	 * CSS class name of drag handle.
	 */
	dragHandleClassName: "dragHandle",
	
	/**
	 * @var object jQuery object of root UL.
	 */
	$root: null,
	
	/**
	 * Initialization.
	 * @param id root UL id.
	 * @returns
	 */
	init: function(id) {
		// @var object this
		var $this = this;
		$this.$root = $('#' + id);
		
		$this.$root.aciSortable({
			child: 100,
			handle: "." + $this.dragHandleClassName,
			scroll: 2,
			end: function(item, hover, placeholder, helper) {
				$.fn.aciSortable.defaults.end.apply(this, arguments);
				// Fix bug
				// Delete empty UL 
				// Note: Когда у LI(1) есть вложенный UL(1), и из UL(1) удаляешь последний LI, 
				// после нельзя добавить в LI(1) вложенный элемент.
				$this.$root.find('ul').each(function(i) {
					if(!$(this).html()) $(this).remove();
				});
				
				// save new ordering
				$this.save();
			}  
			// placeholder: '<li class="ui-state-highlight"></li>',
			// placeholderSelector: '.aciSortablePlace2',
			// start: function(item, placeholder, helper) {
	        //     item.hide();
	        //     placeholder.html(item.html());
	        //     // use the default implementation
	        //     $.fn.aciSortable.defaults.start.apply(this, arguments);
	        // },
		});
	},
	
	/**
	 * Get items
	 * @return array each item of array like as 
	 * {id: <item id from "data-item" attribute>,
	 *  visible: <visible(true)/hidden(false) value> 
	 *  childs: <array of childs like as it> }
	 */
	getItems: function() {
		// @var function get items
		// @param object jQuery object of parent UL.
		var funcGetItems = function($parentUL) {
			var $result = [];
			$($parentUL).children("li").each(function(i) {
				var id = $(this).attr("data-item");
				var visible = ($(this).children(":checkbox[name='visible']:checked").length > 0) ? 1 : 0;
				var childs = funcGetItems($(this).children("ul:first"));
					
				$result.push({id: id, visible: visible, childs: childs});				
			});
			return $result;
		} 
		
		return funcGetItems(this.$root);
	},
	
	/**
	 * Save items ordering
	 */
	save: function() {
		 $.post(this.urls.save, {items: this.getItems()}, function(json) {
			 if(!json.success) alert(json.errorDefaultMessage);
		 }, "json");
	}
}