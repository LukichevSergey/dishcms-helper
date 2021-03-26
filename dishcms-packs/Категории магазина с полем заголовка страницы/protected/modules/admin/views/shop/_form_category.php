<div class="form">

    <?php $form = $this->beginWidget('CActiveForm', array(
        'id'=>'category-form',
        'enableClientValidation'=>true,
        'clientOptions'=>array(
            'validateOnSubmit'=>true,
            'validateOnChange'=>false
        )
    )); ?>

    <div class="row">
        <?php echo $form->labelEx($model, 'title'); ?>
        <?php echo $form->textField($model, 'title'); ?>
        <?php echo $form->error($model, 'title'); ?>
    </div>

    <!--div class="row">
        <?php echo $form->labelEx($model, 'parent_id'); ?>
        <?php /*echo $form->textField($model, '');*/ ?>
        <?php echo $form->error($model, 'title'); ?>
    </div-->

    <div class="row">
        <?php echo $form->labelEx($model, 'page_title'); ?>
        <?php echo $form->textField($model, 'page_title'); ?>
        <?php echo $form->error($model, 'page_title'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'description'); ?>
        <?php $this->widget('admin.widget.CmsEditor.CmsEditor', array('model'=>$model, 'attribute'=>'description')); ?>
        <?php echo $form->error($model, 'description'); ?>
    </div>

    <?php if (!$model->isNewRecord): ?>
    <?php $this->widget('admin.widget.ajaxUploader.ajaxUploader', array(
        'fieldName'=>'images',
        'fieldLabel'=>'Загрузка фото',
        'model'=>$model,
        'fileType'=>'image'
    )); ?>

    <?php $this->widget('admin.widget.ajaxUploader.ajaxUploader', array(
        'fieldName'=>'files',
        'fieldLabel'=>'Загрузка файлов',
        'model'=>$model,
    )); ?>
    <?php endif; ?>

    <div class="row buttons">
        <div class="left">
		    <?php echo CHtml::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', array('class'=>'default-button')); ?>
            <?php echo CHtml::link('Отмена', array('index')); ?>
        </div>

        <?php if (!$model->isNewRecord && !count($model->tovars)): ?>
        <div class="right with-default-button">
            <a href="<?php echo $this->createUrl('shop/categoryDelete', array('id'=>$model->id)); ?>"
               onclick="return confirm('Вы действительно хотите удалить категорию?')">Удалить категорию</a>
        </div>
        <?php endif; ?>
        <div class="clr"></div>
	</div>

    <?php $this->endWidget(); ?>
</div><!-- form -->
