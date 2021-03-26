/**
 * Скрипт для виджета \ecommerce\ext\sync\widgets\Sync 
 */
var ecommerce_ext_sync_widgets_Sync=(function(){
	var _this={};
	
	_this.wrapperClass=".js-sync-catalog";
	_this.url='';
	_this.token='';
	_this.count=0;
	
	function btn() {
		return $(_this.wrapperClass).find(".btn");
	}
	
	function progress() {
		return $(_this.wrapperClass).find(".progress-bar");
	}
	
	function set_progress(val, current) {
		progress().attr("aria-valuenow", val);
		progress().css("width", val + "%");
		$(_this.wrapperClass).attr("title", "Синхронизировано товаров " + current + " из " + _this.count);
	}
	
	function error(errors) {
		console.log("Ошибка:\n" + errors.join("\n"));
		_this.syncDone(true);
	}
	
	function warning(warnings) {
		console.log("Предупреждение:\n" + warnings.join("\n"));
	}
	
	/**
	 * Инициалиация
	 */
	_this.init=function(options) {
		if(typeof(options.url) != "undefined") {
			_this.url=options.url;
			$(document).on("click", _this.wrapperClass + " .btn", _this.onSyncButtonClick);
		}
	};
	
	_this.onSyncButtonClick=function(e) {
		e.preventDefault();
		btn().button("loading");
		$.post(_this.url, {mode:"init"}, _this.syncInitResponse, "json").fail(_this.fail500);
		return false;	
	};
	
	_this.fail500=function() {
		error(["Удаленный сервер не доступен"]);					
	};
	
	_this.syncInitResponse=function(response) {
		if(typeof(response)=="undefined") {
			_this.fail500();
		}
		else if(response.success) {
			_this.token=response.data.token;
			_this.count=response.data.count;
			if(+response.data.count > 0) {
				progress().parent().addClass("progress-striped active");
				progress().removeClass("progress-bar-success").addClass("progress-bar-warning");
				set_progress(0, 0);
				progress().parent().show();
				$.post(_this.url, {mode:"get", "token":_this.token}, _this.syncGetResponse, "json").fail(_this.fail500);
			}
			else {
				_this.syncDone();
			}
		}
		else {
			error(response.errors);
		}
	};
	
	_this.syncGetResponse=function(response) {
		if(typeof(response)=="undefined") {
			_this.fail500();
		}
		else if(response.success) {
			if((typeof(response.data.warnings) != "undefined") && (response.data.warnings.length > 0)) {
				warning(response.data.warnings);
			}
			
			if(response.data.percent === 100) {				
				_this.syncDone();
			}
			else {
				set_progress(response.data.percent, response.data.current);
				$.post(_this.url, {mode:"get", "token":_this.token}, _this.syncGetResponse, "json").fail(_this.fail500);
			}
		}
		else {
			error(response.errors);
		}
	};
	
	_this.syncDone=function(hasError) {
		hasError=(hasError === true);
		if(!hasError) {
			set_progress(100, _this.count);		
			progress().removeClass("progress-bar-warning").addClass("progress-bar-success");
			progress().parent().removeClass("progress-striped active");
			btn().removeClass("btn-info").addClass("btn-success").html("Синхронизация завершена <i class=\"glyphicon glyphicon-ok\"></i>");
			setTimeout(function(){
				btn().removeClass("btn-success").addClass("btn-info").attr("Идет синхронизация...");
				btn().button("reset");
				progress().parent().hide();
				window.location.reload();
			}, 5000);			
		}
		else {
			progress().removeClass("progress-bar-warning").addClass("progress-bar-danger");
			progress().parent().removeClass("progress-striped active");
			btn().removeClass("btn-info").addClass("btn-danger").html("Синхронизация прервана <i class=\"glyphicon glyphicon-remove\"></i>");
			setTimeout(function(){
				btn().removeClass("btn-danger").addClass("btn-info").attr("Идет синхронизация...");
				btn().button("reset");
				progress().parent().hide();
			}, 5000);	
		}
	}
	
	return _this;
})();