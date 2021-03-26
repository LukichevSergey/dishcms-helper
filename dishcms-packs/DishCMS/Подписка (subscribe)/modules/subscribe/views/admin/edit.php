<?
use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;

Y::js('subscribe_edit', 'jQuery("#subscribe_models_Messages_send_time").datetimepicker();');
?>
<h1>Новое сообщение</h1>
<div class="paddos" style="margin-bottom:30px">
    <?= CHtml::link('К списку сообщений', '/cp/subscribe/list', ['class'=>'btn btn-default']); ?>
</div>
<div class="form">
    <? $form=$this->beginWidget('CActiveForm', array(
        'id'=>'subscribe-form',
        'enableClientValidation'=>true,
        'clientOptions'=>array(
            'validateOnSubmit'=>true,
            'validateOnChange'=>false
        ),
        // 'htmlOptions' => array('enctype'=>'multipart/form-data'),
    )); ?>

    <? $this->widget('\common\widgets\form\TextField', A::m(compact('form', 'model'), ['attribute'=>'theme'])); ?>
    <? $this->widget('\common\widgets\form\TinyMceField', A::m(compact('form', 'model'), [
        'attribute'=>'message', 
        'full'=>false
    ])); ?>
    <? if(!$model->from_name) !$model->from_name=\Yii::app()->name; ?>
    <? if(!$model->from) !$model->from='subscribe@'.\Yii::app()->params['domain']; ?>
    <? $this->widget('\common\widgets\form\TextField', A::m(compact('form', 'model'), ['attribute'=>'from_name'])); ?>
    <? $this->widget('\common\widgets\form\TextField', A::m(compact('form', 'model'), ['attribute'=>'from'])); ?>
    <div class="row buttons">
        <?= CHtml::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', array('class'=>'btn btn-primary')); ?>
        <?= CHtml::link('К списку сообщений', '/cp/subscribe/list', ['class'=>'btn btn-default']); ?>
    </div>
<? $this->endWidget(); ?>
</div>