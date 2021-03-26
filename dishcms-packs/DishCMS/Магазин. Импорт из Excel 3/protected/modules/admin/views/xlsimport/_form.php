<?php
use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;

Y::js(false, '$(document).on("submit", "#xls-import-form", function(e) {
    var msg="При импорте файла произойдет полное удаление:\n", isClearMode=false;
    var chboxs={products: "товаров", categories: "категорий каталога", eav_attributes: "дополнительных атрибутов товара"}, k;
    for(k in chboxs) {
        if($("#XlsImportForm_clear_"+k+":checked").length>0) {
            msg+="- " + chboxs[k] + ";\n";
            isClearMode=true;
        }
    }
    msg+="Продолжить импорт товаров?";
    if(isClearMode) return confirm(msg);
    return true;
});', \CClientScript::POS_READY); 
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

	<?= $form->errorSummary($model); ?>

	<div class="alert alert-info">
		<p style="padding:5px" class="alert alert-danger"><span class="label label-danger">ВНИМАНИЕ!</span>
		<br/><span style="font-size:0.9em;color:#000">Перед импортом товаров необходимо загрузить соответствующие изображения в форме загрузки изображений расположенной ниже.</span></p>
	    Папка загрузки для картинок: <b>папка_сайта<?=$model->images_path?></b>
		<br/>
		Имена файлов должны соответствовать именам, указанным в файле импорта.
    </div>
    
	<?php $this->widget('\common\widgets\form\FileField', A::m(compact('form', 'model'), [
		'attribute'=>'filename',
		'htmlOptions'=>['class'=>'form-control w50']
	])); ?>
	
	<div class="row">
		<div class="col-md-3">
    		<?php $this->widget('\common\widgets\form\NumberField', A::m(compact('form', 'model'), [
        		'attribute'=>'limit',
        		'htmlOptions'=>['class'=>'form-control w50']
        	])); ?>
		</div>
		<div class="col-md-3">
    		<?php $this->widget('\common\widgets\form\NumberField', A::m(compact('form', 'model'), [
        		'attribute'=>'delay',
        		'htmlOptions'=>['class'=>'form-control w50']
        	])); ?>
		</div>
	</div>
	<div class="row">
		<div class="col-md-3">
    		<?php $this->widget('\common\widgets\form\NumberField', A::m(compact('form', 'model'), [
        		'attribute'=>'sheet',
        		'htmlOptions'=>['class'=>'form-control w50']
        	])); ?>
		</div>
		<div class="col-md-2">
    		<?php $this->widget('\common\widgets\form\NumberField', A::m(compact('form', 'model'), [
        		'attribute'=>'header_row',
        		'htmlOptions'=>['class'=>'form-control w50']
        	])); ?>
		</div>
		<div class="col-md-4"> 
    		<?php $this->widget('\common\widgets\form\NumberField', A::m(compact('form', 'model'), [
        		'attribute'=>'eav_first_column',
        		'htmlOptions'=>['class'=>'form-control w50']
        	])); ?>
		</div>
	</div>
	
	<div class="row">
		<div class="col-md-12"> 
			<? $this->widget('\common\widgets\form\CheckboxField', A::m(compact('form', 'model'), ['attribute'=>'clear_eav_attributes'])); ?>
			<? $this->widget('\common\widgets\form\CheckboxField', A::m(compact('form', 'model'), ['attribute'=>'clear_products'])); ?>
			<? $this->widget('\common\widgets\form\CheckboxField', A::m(compact('form', 'model'), ['attribute'=>'clear_categories'])); ?>
		</div>		
	</div>
	
	<div class="row buttons">
        <div class="left">
            <?=CHtml::submitButton('Импортировать', ['class'=>'btn btn-primary']); ?>
        </div>
        <div class="clr"></div>
    </div>

	<?php $this->endWidget(); ?>
</div>

