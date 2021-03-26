<?php
/** @var \CActiveForm $form */
/** @var \accounts\models\AccountSettings $model */
use common\components\helpers\HArray as A;

?><div class="form"><?
    $form=$this->beginWidget('\CActiveForm', [
        'id'=>'account-settings-form',
        'enableClientValidation'=>true,
        'clientOptions'=>[
            'validateOnSubmit'=>true,
            'validateOnChange'=>false
        ],
        // 'htmlOptions'=>['enctype'=>'multipart/form-data'],
    ]);

    echo $form->errorSummary($model);

    $this->widget('zii.widgets.jui.CJuiTabs', [
        'tabs'=>[
            'Общие'=>['content'=>$this->renderPartial('accounts.modules.admin.views.settings._account_settings_main', compact('model', 'form'), true), 'id'=>'tab-main'],
            'Регистрация'=>['content'=>$this->renderPartial('accounts.modules.admin.views.settings._account_settings_reg', compact('model', 'form'), true), 'id'=>'tab-reg'],
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
