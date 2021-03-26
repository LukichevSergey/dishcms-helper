/**
 * Скрипт для виджета \extend\modules\polls\widgets\Poll 
 */
window.extendModulesPollsWidgetsPoll=(function(){
	var _this={
		form: null,
		options: {}
	};
	
	function o(name, def) {
		if(typeof _this.options[name] !== "undefined") return _this.options[name];
		return (typeof def !== "undefined") ? def : null; 
	}
	
	_this.init=function(options) {
		_this.options=options;
		_this.form=$("#"+o("form"));
	}
	
	_this.getFormData=function() {
		return _this.form.serialize();
	};
	
	_this.validateRequired=function() {
		var empty={};
		_this.form.find(".required").removeClass("error");
		_this.form.find(".required").each(function(){$(this).parent().removeClass("error");});
		_this.form.find(".required:not(:checked)").each(function(){
			if(!$("[name='"+$(this).attr("name")+"']:checked").length){
				$(this).parent().addClass("error");
				$(this).addClass("error");
				empty[$(this).attr("name")]=true;
			}
		});
		return $.isEmptyObject(empty);
	};
	
	_this.onAjaxBeforeSend=function(jqXHR, settings) {
		if(_this.validateRequired()) {
			settings.data=_this.getFormData();
			return true;
		}
		return false;
	};
	
	return _this;
})();