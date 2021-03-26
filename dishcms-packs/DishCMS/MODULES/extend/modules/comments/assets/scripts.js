/**
 * Основной скрипт для модуля extend\comments 
 */
;window.extendCommentsCrud=(function(){
	var _this={};
	
	function set_select_options($select, data) {
		$select.find("option[value!='']").remove();
		for(var val in data) {
			$select.append('<option value="' + val + '">' + data[val] + '</option>');
		}
	}
	
	_this.init=function() {
		$(document).on("change", "#js__comment_form-parents", _this.onChangeParents);
		$(document).on("change", "#js__comment_form-parent", _this.onChangeParent);
		$(document).on("change", "#js__comment_filter-parents", _this.onChangeFilterParents);
		$(document).on("change", "#js__comment_filter-parent", _this.onChangeFilterParent);
		$(document).on("change", "#js__comment_filter-model", _this.onChangeFilterModel);
	};
	
	_this.onChangeParents=function(e) {
		$.post("/cp/crud/ajax", {
			cid: "extend_comments",
			action: "getParentList",
			hash: $(e.target).val()
		}, _this.onResponseGetParentList, "json");
	};
	
	_this.onResponseGetParentList=function(response) {
		var data=response.success ? response.data : {};
		set_select_options($("#js__comment_form-parent"), data);
		set_select_options($("#js__comment_form-model"), {});
	};
	
	_this.onChangeParent=function(e) {
		$.post("/cp/crud/ajax", {
			cid: "extend_comments",
			action: "getModelList",
			hash: $("#js__comment_form-parents").val(),
			id: $(e.target).val()
		}, _this.onResponseGetModelList, "json");
	};
	
	_this.onResponseGetModelList=function(response) {
		var data=response.success ? response.data : {};
		set_select_options($("#js__comment_form-model"), data);
	};
	
	_this.onChangeFilterParents=function(e) {
		if(!$(e.target).val()) {
			set_select_options($("#js__comment_filter-parent"), {});
			set_select_options($("#js__comment_filter-model"), {});
			$.fn.yiiGridView.update('crudCommentsGridViewId', {data: {hash: "", parent_id: "", model_id: ""}});
		}
		else {
			$.post("/cp/crud/ajax", {
				cid: "extend_comments",
				action: "getParentList",
				hash: $(e.target).val()
			}, _this.onResponseGetFilterParentList, "json");
		}
	};
	
	_this.onChangeFilterParent=function(e) {
		$.post("/cp/crud/ajax", {
			cid: "extend_comments",
			action: "getModelList",
			hash: $("#js__comment_filter-parents").val(),
			id: $(e.target).val()
		}, _this.onResponseGetFilterModelList, "json");
	};
	
	_this.onChangeFilterModel=function(e) {
		$.fn.yiiGridView.update('crudCommentsGridViewId', {data: {
			hash: $("#js__comment_filter-parents").val(),
			model_id: $(e.target).val()
		}});
	};
	
	_this.onResponseGetFilterParentList=function(response) {
		var data=response.success ? response.data : {};
		set_select_options($("#js__comment_filter-parent"), data);
		set_select_options($("#js__comment_filter-model"), {});
	};
	
	_this.onResponseGetFilterModelList=function(response) {
		var data=response.success ? response.data : {};
		$.fn.yiiGridView.update('crudCommentsGridViewId', {data: {
			hash: $("#js__comment_filter-parents").val(),
			parent_id: $("#js__comment_filter-parent").val(),
			model_id: ""
		}});
		set_select_options($("#js__comment_filter-model"), data);
	};
	
	return _this;
})();