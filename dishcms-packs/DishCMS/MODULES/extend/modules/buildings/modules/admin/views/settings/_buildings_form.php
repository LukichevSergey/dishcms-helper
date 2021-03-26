<?php
use common\components\helpers\HArray as A;
?>
<div class="form"><? 
    $form=$this->beginWidget('\CActiveForm', [
        'id'=>'settings-form',
        'enableClientValidation'=>true,
        'clientOptions'=>[
            'validateOnSubmit'=>true,
            'validateOnChange'=>false
        ],
        'htmlOptions'=>['enctype'=>'multipart/form-data'],
    ]); 
    
    echo $form->errorSummary($model); 
    
    $this->widget('zii.widgets.jui.CJuiTabs', [
        'tabs'=>[
            'Основые'=>['content'=>$this->renderPartial('extend.modules.buildings.modules.admin.views.settings._buildings_form_main', compact('model', 'form'), true), 'id'=>'tab-main'],
            'Текст'=>['content'=>$this->renderPartial('extend.modules.buildings.modules.admin.views.settings._buildings_form_text', compact('model', 'form'), true), 'id'=>'tab-text'],
            'Нижний текст'=>['content'=>$this->renderPartial('extend.modules.buildings.modules.admin.views.settings._buildings_form_text_bottom', compact('model', 'form'), true), 'id'=>'tab-text-bottom']
        ],
        'options'=>[]
    ]);
    
    ?>
    
    <div class="row buttons">
      <div class="left">
        <?= CHtml::submitButton('Сохранить', ['class'=>'btn btn-primary']); ?>
      </div>
      <div class="clr"></div>
    </div>
    <? $this->endWidget(); ?>
</div>