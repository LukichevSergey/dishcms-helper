<?
use common\components\helpers\HArray as A;
?>
<div id="question-form-div" class="form">
    <h2>Задать вопрос</h2>

    <?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'question-form',
        'enableClientValidation'=>true,
        'clientOptions'=>array(
            'validateOnSubmit'=>true,
            'validateOnChange'=>false,
            'afterValidate'=>'js: function(form, data, hasError) {submitForm(form, hasError);}'
        )
    )); ?>

    <div class="row">
        <?php echo $form->labelEx($model,'username'); ?>
        <?php echo $form->textField($model,'username',array('maxlength'=>255)); ?>
        <?php echo $form->error($model,'username'); ?>
    </div>

	<div class="row">
		<?php echo $form->labelEx($model,'question'); ?>
		<?php echo $form->textArea($model,'question',array('maxlength'=>255)); ?>
		<?php echo $form->error($model,'question'); ?>
	</div>
	
	<div class="row captcha_wrap">
         <div class="captcha_img" title="Нажмите, чтобы обновить картинку" style="cursor:pointer;display: inline-block;">
	         <?$this->widget('\CCaptcha', [
    	         'captchaAction' => 'captcha',
                 'clickableImage'=>true,
                 'showRefreshButton'=>false
             ])?>
         </div>
         <div class="form_div" style="display:inline-block;width:50%;position:relative;right:2px;vertical-align:top;">
         	<?= $form->textField($model,'verify_code', ['placeholder'=>'Введите код...', 'style'=>'width: 100%;']); ?>
            <div style="display:none"><?= $form->error($model,'verify_code'); ?></div>
         </div>
    </div>

	<div class="row">
		<?php echo $form->checkBox($model,'privacy_policy',array('class'=>'inpt inpt-privacy_policy')); ?>
        <?php echo $form->labelEx($model,'privacy_policy'); ?>
        <div style="display:none"><?php echo $form->error($model,'privacy_policy'); ?></div>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Отправить'); ?>
	</div>
    <?php $this->endWidget(); ?>
</div><!-- form -->
