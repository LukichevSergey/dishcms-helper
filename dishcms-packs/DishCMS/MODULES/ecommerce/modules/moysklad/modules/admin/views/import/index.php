<?php
/** @var \ecommerce\modules\moysklad\modules\admin\controllers\ImportController $this */
use ecommerce\modules\moysklad\components\helpers\HMoySklad;
use ecommerce\modules\order\models\Order;

?>
<h1>Обмен данными с сервисом "Мой Склад"</h1>

<div class="row">
	<div class="col-md-12">
    	<a href="/cp/settings/moysklad_settings" class="btn btn-warning pull-right"><i class="glyphicon glyphicon-cog"></i> Настройки</a>
        <?php
        $this->widget('\common\ext\iterator\widgets\Button', [
            'label'=>'Выгрузить товары с сервиса &laquo;Мой Склад&raquo;',
            'iterator'=>'ecommerce.modules.moysklad.config.iterators.moysklad.import',
            'tagOptions'=>['class'=>'', 'style'=>'width:80%'],
            'htmlOptions'=>[
                'encode'=>false, 
                'class'=>'btn btn-primary js-import-btn',
                'style'=>'margin-right:5px;margin-bottom:12px;width:370px','data-loading-text'=>'Подождите, идет выгрузка товаров...',
                'onclick'=>'$(\'.js-import-message,.js-import-error,.js-import-success\').hide();'
            ],
            'progressOptions'=>['style'=>'display:none;width:370px;height:7px;top:36px;position:absolute;'],
            'jsProcess'=>'try{$(".js-import-error").hide();$(".js-import-message").text(response.data.ipm.message).show();}catch{}',
            'jsError'=>'try{$(".js-import-error").html("Произошла ошибка: " + response.errors.join("<br/>")).show();}catch{}window.ecommerceMoySkladImportIteration=0;',
            'jsDone'=>'try{$(".js-import-error,.js-import-message").hide();$(".js-import-success").html("Импорт товаров успешно завершен!").show();}catch{}window.ecommerceMoySkladImportIteration=0;',
        ]);
        ?>
        <div class="alert alert-danger js-import-error" style="display:none"></div>
        <div class="alert alert-info js-import-message" style="display:none"></div>
        <div class="alert alert-success js-import-success" style="display:none"></div>
	</div>
	<div class="col-md-12">
		<label>
			<input type="radio" name="import_mode" class="js-import-mode" value="rests" checked="checked">			
			Частичная выгрузка
		</label>
		&nbsp;&nbsp;&nbsp;&nbsp;
		<label>
			<input type="radio" name="import_mode" class="js-import-mode" value="full">			
			Полная выгрузка
		</label>
		<div class="alert alert-info">
    		<strong>Полная выгрузка</strong> - будут выгружены только новые товары и новые торговые предложения.<br/>
    		<strong>Частичная выгрузка</strong> - будут обновлены только цены, остатки, единицы измерения у существующих товаров и остатки у существующих торговых предложений.
		</div>
		<div class="alert alert-warning">
			Сопоставление торговых предложений происходит по полному соответствию значений всех свойств торгового предложения
		</div>
	</div>
</div>
<script>
window.ecommerceMoySkladImportIteration=0;
$(document).ready(function() {
    function error(msg) {let err=$(".js-import-error");if(typeof msg == 'undefined'){err.html('').hide();}else{err.html(msg).show();}}
    $(document).on('click', '.js-import-btn', function(e) {
    	let mode=$('.js-import-mode:checked').val();
        $('.js-import-message, .js-import-error, .js-import-success').hide();
        if(!mode.length){error('Необходимо выбрать режим обмена');}
        else if($.inArray(mode, ['rests', 'full']) < 0) {
            error('Выбран неизвестный режим обмена');
        }
        else { return true; }
        e.stopImmediatePropagation();return false;
    });
    $(document).on('commonExtIterator.onBeforeSendModifyData', function(e, data) {
    	let mode=$('.js-import-mode:checked').val();
        if(!window.ecommerceMoySkladImportIteration && mode.length) {
            data.append('mode', mode);
        }
        window.ecommerceMoySkladImportIteration++;
    });
});
</script>
