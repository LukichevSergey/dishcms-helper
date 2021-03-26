<?php
/** @var \DListBoxAttribute\widgets\admin\AdminWidget $this */
/** @var \DListBoxAttribute\models\DListBoxAttribute $model */
?>
<div class="form">
	<?php $form = $this->owner->beginWidget('\CActiveForm', array(
        'id'=>'product-form',
        'enableClientValidation'=>true,
        'clientOptions'=>array(
            'validateOnSubmit'=>true,
            'validateOnChange'=>false
        )
	));?>
		<div class="row">
			<?php echo $form->labelEx($model, 'title'); ?>
			<?php echo $form->textField($model, 'title'); ?>
			<?php echo $form->error($model, 'title'); ?>
		</div>
		
		<div class="row buttons">
        <div class="left">
		    <?php echo CHtml::submitButton($model->isNewRecord ? 'Добавить' : 'Сохранить', array('class'=>'default-button')); ?>
            <?php echo HtmlHelper::linkBack('Отмена'); ?>
        </div>
        <div class="clr"></div>
	</div>
	<?php $this->owner->endWidget(); ?>
</div>