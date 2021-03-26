<?php
/* @var PageController $this */
?>
<div class="form">
    <?php $form = $this->beginWidget('CActiveForm', array(
        'id'=>'page-form',
        'enableClientValidation'=>true,
        'clientOptions'=>array(
            'validateOnSubmit'=>true,
            'validateOnChange'=>false
        ),
        'htmlOptions' => array('enctype'=>'multipart/form-data'),
    )); ?>

    <?php 

    $tabs = array(
            'Основное'=>array('content'=>$this->renderPartial('_form_product', compact('model', 'form'), true), 'id'=>'tab-general'),
            'Seo'=>array('content'=>$this->renderPartial('_form_product_seo', compact('model', 'form'), true), 'id'=>'tab-seo'),            
            'Видео'=>array('content'=>$this->renderPartial('_form_product_video', compact('model', 'form'), true), 'id'=>'tab-video'),            
        );

    if(Yii::app()->params['attributes'])
        $tabs['Атрибуты'] = array('content'=>$this->renderPartial('_form_product_attributes', compact('model', 'form', 'fixAttributes'), true), 'id'=>'tab-attrs');

       
    $this->widget('zii.widgets.jui.CJuiTabs', array(
        'tabs'=> $tabs,
        'options'=>array()
    )); ?>

    <div class="row buttons">
        <div class="left">
            <?php echo CHtml::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', array('class'=>'default-button')); ?>
            <?php echo CHtml::link('Отмена', array('index')); ?>
        </div>

        <?php if (!$model->isNewRecord): ?>

         <div class="left with-default-button">
            <a href="<?php echo $this->createUrl('shop/productclone', array('id'=>$model->id)); ?>">Клонировать товар</a>
        </div>

        <div class="right with-default-button">
            <a href="<?php echo $this->createUrl('shop/productDelete', array('id'=>$model->id)); ?>"
               onclick="return confirm('Вы действительно хотите удалить товар?')">Удалить товар</a>
        </div>
        <?php endif; ?>
        <div class="clr"></div>
    </div>

    <?php $this->endWidget(); ?>
</div><!-- form -->
