---------------------------------------
Подключение
---------------------------------------
1) Подключить crud конфигурацию 'common.ext.iterator.config.crud'
2) Добавить событие получения секретного ключа шифрования обмена 'onCommonExtIteratorGetSecureKeys'
3) Создать конфигурацию. Примеры в папке common\ext\iterator\config\iterators

---------------------------------------
Использование
---------------------------------------
1) Пример виджета кнопки

$this->widget('\common\ext\iterator\widgets\Button', [
    'label'=>'Запустить',
    'iterator'=>'common.ext.iterator.config.iterators.test.iterator', 
    'htmlOptions'=>['class'=>'btn btn-info','style'=>'margin-right:5px;margin-bottom:10px;width:170px','data-loading-text'=>'Подождите...'],
    'progressOptions'=>['style'=>'display:none;width:170px;height:7px;top:36px;position:absolute;']
]);

2) Пример использования виджета с загрузкой файла (без проверки типа)
<div class="row">
    <div class="col-md-12">
        <div class="row">
        	<div class="col-md-12">
                <label>
                	<input type="file" class="js-import-file">
                </label>
            </div>
        </div>
        <?php
        $this->widget('\common\ext\iterator\widgets\Button', [
            'label'=>'Загрузить товары из Excel',
            'iterator'=>$iterator,
            'data'=>[
                'config'=>$config // конфигурация обработчика данных
            ],
            'tagOptions'=>['class'=>'', 'style'=>'width:80%'],
            'htmlOptions'=>[
                'encode'=>false, 
                'class'=>'btn btn-primary js-import-btn',
                'style'=>'margin-right:5px;margin-bottom:12px;width:370px','data-loading-text'=>'Подождите, идет импорт товаров...',
            ],
            'progressOptions'=>['style'=>'display:none;width:370px;height:7px;top:36px;position:absolute;'],
            'jsProcess'=>'try{$(".js-import-error").hide();$(".js-import-message").text(response.data.ipm.message).show();}catch{}',
            'jsError'=>'try{$(".js-import-error").html("Произошла ошибка: " + response.errors.join("<br/>")).show();}catch{}',
            'jsDone'=>'try{$(".js-import-error,.js-import-message").hide();$(".js-import-success").html("Импорт товаров успешно завершен!").show();}catch{}',
        ]);
        ?>
        <div class="alert alert-danger js-import-error" style="display:none"></div>
        <div class="alert alert-info js-import-message" style="display:none"></div>
        <div class="alert alert-success js-import-success" style="display:none"></div>
    </div>
</div>
<script>
$(document).ready(function() {
	$(document).on('click', '.js-import-btn', function(e) {
		$('.js-import-message, .js-import-error, .js-import-success').hide();
		let file=$('.js-import-file'), iteration=0;
		$(document).on('commonExtIterator.onBeforeSendModifyData', function(e, data) {
			if(!iteration && file[0].files.length) {
				data.append('filename', file[0].files[0]);
			}
			iteration++;
		});
	});
});
</script>
