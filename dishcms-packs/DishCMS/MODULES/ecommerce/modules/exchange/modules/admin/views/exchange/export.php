<?php
/** @var \ecommerce\modules\exchange\modules\admin\controllers\ExchangeController $this */
/** @var string $iterator путь к конфигурации итератора импорта каталога из Excel */

use common\components\helpers\HHash;
?>
<h1>Экспорт данных в Excel</h1>

<div class="row">
    <div class="col-md-12">
    	<?php /* ?>
        <div class="row">
        	<div class="col-md-12">
                <label>
                	<input type="checkbox" class="js-export-zip"> Архивировать файл выгрузки
                </label>
            </div>
        </div>
        <? /**/ ?>
        <?php /* ?><a href="javascript:;" class="pull-right btn btn-warning js-clean-files">Очистить временные файлы</a><? /**/ ?>
        <?php
        $this->widget('\common\ext\iterator\widgets\Button', [
            'label'=>'Выгрузить товары в Excel',
            'iterator'=>$iterator,
            'data'=>[
                'config'=>HHash::srEcrypt($config, md5($iterator)) // конфигурация обработчика данных
            ],
            'tagOptions'=>['class'=>'', 'style'=>'width:80%'],
            'htmlOptions'=>[
                'encode'=>false, 
                'class'=>'btn btn-primary js-import-btn',
                'style'=>'margin-right:5px;margin-bottom:12px;width:370px','data-loading-text'=>'Подождите, идет экспорт товаров...',
            ],
            'progressOptions'=>['style'=>'display:none;width:370px;height:7px;top:36px;position:absolute;'],
            'jsProcess'=>'try{$(".js-import-error").hide();$(".js-import-message").html(response.data.ipm.message).show();}catch{}',
            'jsError'=>'try{$(".js-import-error").html("Произошла ошибка: " + response.errors.join("<br/>")).show();}catch{}window.ecommerceExchangeImportIteration=0;',
            'jsDone'=>'window.ecommerceExchangeImportIteration=0;try{$(".js-import-error,.js-import-message").hide();
                $(".js-import-success").html("Экспорт товаров успешно завершен!").show();
                window.location.href="/ecommerce/exchange/admin/exchange/downloadExportFile?filename="+response.data.ipm.download;
            }catch{alert("Произошла ошибка! Файл не может быть выгружен");}',
        ]);
        ?>
        <div class="alert alert-danger js-import-error" style="display:none"></div>
        <div class="alert alert-info js-import-message" style="display:none"></div>
        <div class="alert alert-success js-import-success" style="display:none"></div>
    </div>
</div>
