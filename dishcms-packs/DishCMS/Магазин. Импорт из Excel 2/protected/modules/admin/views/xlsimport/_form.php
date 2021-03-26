<?php
use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;

$model->step=A::get($this->stepResult, 'next', 1);
?>
<div class="form">
    <?php $form = $this->beginWidget('CActiveForm', [
        'id'=>'xls-import-form',
        'enableClientValidation'=>true,
        'clientOptions'=>[
            'validateOnSubmit'=>true,
            'validateOnChange'=>false
        ],
        'htmlOptions'=>['enctype'=>'multipart/form-data']
    ]); ?>

	<? $this->widget('\common\widgets\form\HiddenField', A::m(compact('form', 'model'), ['attribute'=>'cached_filename'])); ?>
	<? $this->widget('\common\widgets\form\HiddenField', A::m(compact('form', 'model'), ['attribute'=>'step'])); ?>

	<?=$form->errorSummary($model)?>

	<? if(!is_array($this->stepResult)): ?>
		<div class="alert alert-info">
			<p style="padding:5px" class="alert alert-danger"><span class="label label-danger">ВНИМАНИЕ!</span>
			<br/><span style="font-size:0.9em;color:#000">Перед импортом товаров необходимо загрузить соответствующие изображения в форме загрузки изображений расположенной ниже.</span></p>
    	    Папка загрузки для картинок: <b>папка_сайта<?=$model->images_path?></b>
			<br/>
			Имена файлов должны соответствовать именам, указанным в файле импорта.
	    </div>

		<?php $this->widget('\common\widgets\form\FileField', A::m(compact('form', 'model'), [
			'attribute'=>'import_filename',
			'htmlOptions'=>['class'=>'form-control w50']
		])); ?>

		<? $this->widget('\common\widgets\form\NumberField', A::m(compact('form', 'model'), ['attribute'=>'sheet_page', 'htmlOptions'=>['class'=>'form-control w10']])); ?>
		<div class="alert alert-danger">
		<? $this->widget('\common\widgets\form\CheckboxField', A::m(compact('form', 'model'), ['attribute'=>'clear_catalog'])); ?>
		<? Y::js(false, '$(document).on("click", "#XlsImportForm_clear_catalog", function(e){
if($(e.target).is(":checked")) {
	if(confirm("Подтвердите ПОЛНУЮ ОЧИСТКУ каталога.")) {
		$("#clear_catalog_with_categories_box").show();
		return true;
	}
} else {
	$("#clear_catalog_with_categories_box").hide();
	$("#XlsImportForm_clear_catalog_with_categories").prop("checked", false);
	return true;
}
return false;
});', \CClientScript::POS_READY); ?>
			<div id="clear_catalog_with_categories_box" style="margin-left:55px;border:1px dashed;padding-left:15px;padding-top:10px;<?=$model->clear_catalog?'':'display:none'?>">
				<? $this->widget('\common\widgets\form\CheckboxField', A::m(compact('form', 'model'), ['attribute'=>'clear_catalog_with_categories'])); ?>
				<? Y::js(false, '$(document).on("click", "#XlsImportForm_clear_catalog_with_categories", function(e){
					return $(e.target).is(":checked") ? confirm("Подтвердите УДАЛЕНИЕ категорий при очистке каталога.") : true;
				});', \CClientScript::POS_READY); ?>
			</div>
		</div>
	<? else: ?>
		<? $this->widget('\common\widgets\form\HiddenField', A::m(compact('form', 'model'),  ['attribute'=>'sheet_page'])); ?>
	<? endif; ?>

	<? if($this->stepResult): ?>
	<div class="row buttons">
		<div class="alert alert-success">
		Импорт текущего шага <b><?=$this->stepResult['step']?></b> успешно завершен.
		</div>
		<div class="alert alert-info">
		Импортировано товаров: <b><?=(int)$this->stepResult['start']+(int)$this->stepResult['count']?></b> из <b><?=$this->stepResult['total']?></b>
		</div>
		<div class="center" style="text-align:center">
            <?=CHtml::submitButton('Запустить шаг '.$this->stepResult['next'].' из '.$this->stepResult['last'], ['class'=>'btn btn-warning', 'style'=>'padding:5px 40px']); ?>
			<? $this->widget('\common\widgets\form\CheckboxField', A::m(compact('form', 'model'), ['attribute'=>'auto'])); ?>
			<? if($model->auto): ?>
			<p>Процесс будет продолжен автоматически через 5 секунд.</p>
			<? Y::js(false, 'setTimeout(function(){if(!$("#XlsImportForm_auto").prop("checked")){return;}$("#xls-import-form").submit();$("#xls-import-form .row.buttons").html($.parseHTML("<div class=\"alert alert-info\">Процесс запущен</div>"));},5000);', \CClientScript::POS_READY); ?>
			<? endif; ?>
        </div>
        <div class="clr"></div>
	</div>
	<? else: ?>
	<div class="row buttons">
        <div class="left">
            <?=CHtml::submitButton('Импортировать', ['class'=>'btn btn-primary']); ?>
        </div>
        <div class="clr"></div>
    </div>
	<? endif; ?>

	<?php $this->endWidget(); ?>
</div>

