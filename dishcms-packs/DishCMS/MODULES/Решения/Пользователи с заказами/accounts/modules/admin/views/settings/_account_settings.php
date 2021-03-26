<?php
/** @var \CActiveForm $form */
/** @var \accounts\models\AccountSettings $model */
use common\components\helpers\HYii as Y;

Y::js('settings_tabs_loader', ';$(".js-settings-tabs-loader").hide();$(".js-settings-tabs").show();', \CClientScript::POS_READY);
?>
<div class="alert alert-default js-settings-tabs-loader">Подождите, идет загрузка страницы...</div>
<div class="form js-settings-tabs" style="display: none">
    <div class="form"><?
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
                'Регистрация'=>['content'=>$this->renderPartial('accounts.modules.admin.views.settings._account_settings_reg', compact('model', 'form'), true), 'id'=>'tab-reg'],
                'Авторизация'=>['content'=>$this->renderPartial('accounts.modules.admin.views.settings._account_settings_auth', compact('model', 'form'), true), 'id'=>'tab-auth'],
                'Восстановление пароля'=>['content'=>$this->renderPartial('accounts.modules.admin.views.settings._account_settings_restore', compact('model', 'form'), true), 'id'=>'tab-restore'],
                // 'Страны для номера телефона'=>['content'=>$this->renderPartial('accounts.modules.admin.views.settings._account_settings_phone', compact('model', 'form'), true), 'id'=>'tab-phone'],
                'Безопасность'=>['content'=>$this->renderPartial('accounts.modules.admin.views.settings._account_settings_secure', compact('model', 'form'), true), 'id'=>'tab-secure'],
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
</div>
