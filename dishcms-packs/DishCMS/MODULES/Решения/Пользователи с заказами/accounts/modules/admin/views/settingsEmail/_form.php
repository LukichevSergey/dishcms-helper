<?php
/** @var \CActiveForm $form */
/** @var \accounts\models\AccountSettings $model */
use common\components\helpers\HYii as Y;
use accounts\components\helpers\HAccountEmail;

Y::jsCore('fancybox');
Y::js('settings_tabs_loader', ';$(".js-settings-tabs-loader").hide();$(".js-settings-tabs").show();', \CClientScript::POS_READY);
?>
<div class="alert alert-default js-settings-tabs-loader">Подождите, идет загрузка страницы...</div>
<div class="form js-settings-tabs" style="display: none">
    <div class="panel panel-default">
    	<div class="panel-body" style="padding-bottom:0;">
    		<input type="number" min="0" class="js-accounts-settings-email-account-id form-control inline" style="width:25%" placeholder="Account ID">
    		&nbsp;<button class="btn btn-primary js-accounts-settings-email-view-btn">Показать письмо уведомления</button>
    		&nbsp;<div class="alert alert-info inline" style="font-size:11px;padding:5px;margin-top:-10px;position:relative;top:7px;">
    			Актуальное письмо будет показано только после сохранения настроек
    			<br/>
    			Будет отображен только пример шаблона.
    		</div>
    	</div>
    </div>
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
            'tabs'=>HAccountEmail::getTabs($form, $model),
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
<script>$(document).on('click', '.js-accounts-settings-email-view-btn', function(e){
	$.post('/accounts/admin/default/viewEmailTemplate', {id: $('.js-accounts-settings-email-account-id').val(), ad: $('.js-accounts-settings-email-advert-id').val(), tpl: $('#account-settings-form').find('.ui-tabs li.ui-tabs-active a').attr('href').replace(/#tab-/, '')}, function(r){
		if(r.success){ $.fancybox.open('<div class="message" style="padding:40px">' + r.data.html + '</div>'); }
		else{alert('Произошла ошибка!\nПроверьте корректность указанного ID аккаунта');}
	}, 'json');
	e.preventDefault();return false;
});</script>
<style>#account-settings-form > .ui-tabs > ul.ui-tabs-nav > li a{font-size:13px};</style>
