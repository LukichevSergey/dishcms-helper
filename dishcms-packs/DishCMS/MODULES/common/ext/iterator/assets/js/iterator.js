/**
 * Объект Итератор
 */
window.cmsCommonExtIterator=(function(){
	let _this={};
	
	function is(context,name){return (typeof context != 'undefined') && eval('typeof context.' + name + ' != "undefined"');}
	function get(context,name,def){return is(context,name) ? eval('context.'+name) : ((typeof def != 'undefined') ? def : null);}
	function call(context,name,data,def){if(typeof(def)=='undefined'){def=function(){};}return get(context,name,def).call(_this,data);}
	
	_this.is=is;
	_this.get=get;
	_this.call=call;
	
	// @link https://stackoverflow.com/a/42483509
	function appendFormdata(FormData, data, name) {
		name=name||'';if(typeof data === 'object'){
	        $.each(data, function(index, value){if (name == ''){appendFormdata(FormData, value, index);}else{appendFormdata(FormData, value, name + '['+index+']');}});
	    }else{FormData.append(name, data);}
	}
	
	/**
	 * Запуск следующего шага итерации
	 * @param string url URL обработки итерации
	 * Запрос по переданному URL должен возвращать данные в формате JSON следующей структуры
	 * {
     *     success: boolean, 
     *     data: {
     *         hash: (string) хэш процесса при создании нового процесса, 
     *         percent: (float) процент выполненности процесса, при последующих итерациях. 
     *         params: (mixed) дополнительные параметры, которые будут переданы в следующий запрос
     *     },
     *     при возникновении ошибок
     *     errors: { 
     *         code: message
     *     }
     * } 
     * @param mixed params дополнительные параметры, которые будут переданы в запрос.
	 * @param object options дополнительные параметры
	 *  "hashVar": (string) имя переменной хэша процесса, по умолчанию "h"
	 *  "paramsVar": (string) имя переменной в которой передаются дополнительные параметры, по умолчанию "ipm"
	 *  "progress": (string) JQuery селектор получения DOM-элемента прогресса (bootstrap-3)
	 *  "progressbar": (string) JQuery селектор получения DOM-элемента прогрессбара (bootstrap-3)
	 *  "process": (callable) дополнительный обработчик шага процесса вида function(response){} 
	 *  "done": (callable) дополнительный обработчик по завершению процесса вида function(response){}
	 *  "error": (callable) дополнительный обработчик при ошибке процесса вида function(response){}
	 *  "delay": (int) задержка перед отправкой следующего запроса в милисекундах. По умолчанию 100.
	 * @param mixed rdata (системный параметр) используется для передачи данных предыдущего запроса для внутренней рекурсии
	 * @param string hash (системный параметр) используется для передачи хэша процесса для внутренней рекурсии
	 */
	_this.next=function(url, params, options, rdata, hash) {
		let percent=is(rdata, 'percent') ? parseFloat(get(rdata, 'percent')) : 0;
		if(_this.progress(percent, options)) {
			let data=new FormData,paramsVar=get(options, 'paramsVar', 'ipm');
			if(typeof hash != 'undefined'){data.append(get(options, 'hashVar', 'h'), hash);}
			if(typeof params != 'undefined'){for(k in params){data.append(k,params[k])}}
			if(is(rdata, paramsVar)){appendFormdata(data, get(rdata, paramsVar), paramsVar);}
			$(document).trigger('commonExtIterator.onBeforeSendModifyData', [data]);
			$.ajax({
				type: 'POST',
				url: url,
				data: data, 
				dataType: 'json',
				cache: false,
				processData: false,
	            contentType: false,
				success: function(r) {
					if(r.success) {
						if(is(r.data, 'hash')) { hash=r.data.hash; }
						setTimeout(function() {
							call(options, 'process', r);
							_this.next(url, params, options, r.data, hash);
						}, get(options, 'delay', 100));
					}
					else {
						_this.progress(-1, options);
						_this.error(r, options);
					}
				},
				error: function(xhr, status, error) {
					_this.progress(-1, options);
					_this.error({errors:{0:xhr.responseText}}, options);
				}
			});
		}
		else {
			call(options, 'done', {data: (typeof rdata != 'undefined') ? rdata : {}});
		}		
	};
	
	_this.getErrorMessage=function(response) {
		let message='';
		if(is(response, 'errors')){
			if($.isArray(response.errors)) { message+=response.errors.join('\n'); }
			else { for(let errcode in response.errors){message+=response.errors[errcode] + '\n';}} 
		}
		if(message.length>0){message='Произошли следующие ошибки:\n' + message;}
		else{message='Произошла ошибка!';}	
		return message;
	};
	
	_this.error=function(response, options) {		
		call(options, 'error', response, function(response) {
			alert(_this.getErrorMessage(response));
		});
	},
	
	_this.progress=function(percent, options) {
		let progress=get(options, 'progress'); 
		let $progress=(progress && $(progress).length) ? $(progress) : null;		
		let progressbar=get(options, 'progressbar');
		let $progressbar=(progressbar && $(progressbar).length) ? $(progressbar) : null;
		
		if(isNaN(percent=+percent)){percent=0;}
		
		if($progressbar && (!percent || (percent>0))) {
			$progressbar.attr('aria-valuenow', percent);
			$progressbar.attr('title', percent + '%');
			$progressbar.css('width', percent + '%');
		}
		
		if(!percent) {
			if($progressbar) $progressbar.removeClass('progress-bar-success').removeClass('progress-bar-danger');
			if($progress) {
				$progress.addClass('active').addClass('progress-striped');
				$progress.show();
			}
		}
		else if(percent >= 100) {
			if($progress) $progress.removeClass('active').removeClass('progress-striped');
			if($progressbar) $progressbar.addClass('progress-bar-success');
			return false;
		}
		else if(percent < 0) {
			if($progress) $progress.removeClass('active').removeClass('progress-striped');
			if($progressbar) $progressbar.addClass('progress-bar-danger');
		}
		
		return true;
	}
	
	return _this;
})();
