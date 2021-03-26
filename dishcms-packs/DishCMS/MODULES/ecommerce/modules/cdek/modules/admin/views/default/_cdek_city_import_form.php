<?php
use common\components\helpers\HArray as A;
?>
<div class="panel panel-default">
    <div class="panel-heading">Импорт городов СДЭК</div>
    <div class="panel-body">
        <div class="form">
            <?php $form = $this->beginWidget('\CActiveForm', [
                'id'=>'cdek-city-import-form',
                'enableClientValidation'=>true,
                'clientOptions'=>[
                    'validateOnSubmit'=>true,
                    'validateOnChange'=>false
                ],
                'htmlOptions'=>['enctype'=>'multipart/form-data'],
            ]); ?>

            <?=$form->errorSummary($model)?>
             
            <? $this->widget('\common\widgets\form\FileField', A::m(compact('form', 'model'), ['attribute'=>'filename'])); ?>
            <? $this->widget('\common\widgets\form\CheckboxField', A::m(compact('form', 'model'), ['attribute'=>'skip_exists'])); ?>
            
            <div class="row buttons">
              <div class="left">
                <?=CHtml::submitButton('Импортировать', array('class'=>'btn btn-primary'))?>
              </div>
              <div class="clr"></div>
            </div>
             
        <? $this->endWidget(); ?>
        </div>
    </div>
</div>
