<?php
/** @var \ecommerce\modules\exchange\modules\admin\controllers\ExchangeController $this */
/** @var string $iterator путь к конфигурации итератора импорта каталога из Excel */

use common\components\helpers\HHash;
?>
<h1>Импорт данных из Excel</h1>

<div class="row">
    <div class="col-md-12">
        <?php /* ?><a href="/cp/settings/moysklad_settings" class="btn btn-warning pull-right"><i class="glyphicon glyphicon-cog"></i> Настройки</a> */ ?>
        <div class="row">
        	<div class="col-md-12">
                <label>
                	<input type="file" class="js-import-file">
                </label>
            </div>
        </div>
        <?php /* ?><a href="javascript:;" class="pull-right btn btn-warning js-clean-files">Очистить временные файлы</a><? /**/ ?>
        <?php
        $this->widget('\common\ext\iterator\widgets\Button', [
            'label'=>'Загрузить товары из Excel',
            'iterator'=>$iterator,
            'data'=>[
                'config'=>HHash::srEcrypt($config, md5($iterator)) // конфигурация обработчика данных
            ],
            'tagOptions'=>['class'=>'', 'style'=>'width:80%'],
            'htmlOptions'=>[
                'encode'=>false, 
                'class'=>'btn btn-primary js-import-btn',
                'style'=>'margin-right:5px;margin-bottom:12px;width:370px','data-loading-text'=>'Подождите, идет импорт товаров...',
            ],
            'progressOptions'=>['style'=>'display:none;width:370px;height:7px;top:36px;position:absolute;'],
            'jsProcess'=>'try{$(".js-import-error").hide();$(".js-import-message").html(response.data.ipm.message).show();}catch{}',
            'jsError'=>'try{$(".js-import-error").html("Произошла ошибка: " + response.errors.join("<br/>")).show();}catch{}window.ecommerceExchangeImportIteration=0;',
            'jsDone'=>'window.ecommerceExchangeImportIteration=0;try{$(".js-import-error,.js-import-message").hide();$(".js-import-success").html("Импорт товаров успешно завершен!").show();}catch{}',
        ]);
        ?>
        <div class="alert alert-danger js-import-error" style="display:none"></div>
        <div class="alert alert-info js-import-message" style="display:none"></div>
        <div class="alert alert-success js-import-success" style="display:none"></div>
    </div>
</div>
<script>
window.ecommerceExchangeImportIteration=0;
$(document).ready(function() {
	let file=$('.js-import-file');
	function error(msg) {let err=$(".js-import-error");if(typeof msg == 'undefined'){err.html('').hide();}else{err.html(msg).show();}}
	$(document).on('click', '.js-import-btn', function(e) {
		$('.js-import-message, .js-import-error, .js-import-success').hide();
		if(!file[0].files.length){error('Необходимо выбрать файл для импорта');}
		else if($.inArray(file[0].files[0].type, ['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'/*, 'application/zip'*/]) < 0) {
			error('К загрузке разрешен только файл формата .xlsx'); /*  или zip архив */
		}
		else { return true; }
		e.stopImmediatePropagation();return false;
	});
	$(document).on('commonExtIterator.onBeforeSendModifyData', function(e, data) {
		if(!window.ecommerceExchangeImportIteration && file[0].files.length) {
			data.append('filename', file[0].files[0]);
		}
		window.ecommerceExchangeImportIteration++;
	});
});
</script>
