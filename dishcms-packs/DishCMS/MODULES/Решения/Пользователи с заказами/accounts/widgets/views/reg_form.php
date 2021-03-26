<?php
/** @var \accounts\widgets\RegForm $this */
use common\components\helpers\HYii as Y;
use settings\components\helpers\HSettings;
use crud\models\ar\accounts\models\Account;

Y::module('common')->publishJs('js/tools/activeform.js');
Y::module('common')->publishJs('js/tools/form2object.js');

$settings=HSettings::getById('accounts');
$account=new Account('registration');
?>
<script>function regformv(form, data, hasError){
let af=new kontur_activeform(form, ".popup-form-item", "");let fields=$(form).find("select,textarea,input:not(:submit):not(:button):not([type=radio]):not([type=checkbox]),input:checked");
fields.attr('title', '');if(!hasError){$.post('/accounts/reg/ajaxRegistration',kontur_form2object(fields),function(r){
    	if(r.success === true) {$('#<?=$this->id?>').addClass('successed').html(r.data.text + '<div style="margin-top:40px;text-align:center;width:100%"><a href="javascript:;" class="btn" style="margin:0 auto" onclick="$.fancybox.close();">Закрыть</a></div>');}
    	else {af.modelPrefix='crud_models_ar_accounts_models_Account_';af.errorResponce(r.errors);af.modelPrefix='';}
},'json');}else{af.errorResponce(data);}return false;}
$(document).on('click', '.js-btn-login', function(e){$.fancybox.getInstance('close');$.fancybox.open({src:'#modal-loginform', type:'inline'});});
</script>
<?php if($this->popup): ?><div style="display: none;"><?php endif; ?>
	<div class="fancybox-content<? if($this->popup): ?> fancybox-content-popup<? endif; ?>" id="<?=$this->id?>">
		<div class="reg-form-box">
            <h3>Регистрация</h3>
            <span><?= $settings->reg_form_text; ?></span>
        	<?php $this->widget('\common\widgets\form\ActiveForm', [
        	    'id'=>'accounts__reg-form',
        	    'model'=>$account,
        	    'attributes'=>['name', 'lastname', 'email', 'password', 'repassword', 'is_wholesale'],
        	    'tag'=>false,
        	    'errorSummary'=>false,
        	    'formOptions'=>[
        	        'enableAjaxValidation'=>true,
        	        'enableClientValidation'=>false,
        	        'clientOptions'=>[
        	            'hideErrorMessage'=>false,
        	            'validationUrl'=>'/accounts/reg/ajaxRegistration',
        	            'afterValidate'=>'js:regformv'
        	        ],
        	        'htmlOptions'=>['class'=>'reg-form']
        	    ],
        	    'types'=>[
        	        'password'=>'passwordField',
        	        'repassword'=>'passwordField',
        	        'is_wholesale'=>function($widget, $form, $attribute) {
            	        ?>
            	        <div class="confirmation">
                            <label class="check">
                            	<?= $form->checkBox($widget->model, $attribute, ['class'=>'input-default']); ?>
                                <span class="input-custom"></span>
                                Зарегистрироваться как оптовый покупатель
                            </label>
                            <?= $form->error($widget->model, $attribute); ?>
                        </div>    	        
                        <?php
            	    },
        	    ],
        	    'privacyLabel'=>function($widget, $form) use ($settings) {
        	        ?>
        	        <div class="confirmation gray privacy">
                        <label class="check">
                        	<?= $form->checkBox($widget->model, 'privacy', ['class'=>'input-default']); ?>
                            <span class="input-custom"></span>
                            Подтверждаю свое согласие на <?= \CHtml::link('обработку персональных данных', $settings->privacy_link); ?>
        	        	</label>
                        <?= $form->error($widget->model, 'privacy'); ?>
                    </div>
                    <?php
        	    },
        	    'submitLabel'=>function() {
        	        ?>
        	        <div class="send-btn send-btn-2">
                        <input type="submit" value="Зарегистрироваться">
                    </div>
                    
                    <div class="form-links-box form-link-account">
                        <a href="javascript:;" class="form-links js-btn-login">У меня уже есть аккаунт</a>
                    </div>
        	        <?php 
        	    },
        	    'htmlOptions'=>[
        	        'rowTag'=>'div',
        	        'rowOptions'=>['class'=>'popup-form-item']
        	    ]
        	]); ?>
   		</div>
	</div>	
<?php if($this->popup): ?></div><?php endif; ?>
