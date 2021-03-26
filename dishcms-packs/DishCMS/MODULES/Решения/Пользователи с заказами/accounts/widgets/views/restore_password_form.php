<?php
use common\components\helpers\HYii as Y;
use settings\components\helpers\HSettings;
use crud\models\ar\accounts\models\Account;

Y::module('common')->publishJs('js/tools/activeform.js');
Y::module('common')->publishJs('js/tools/form2object.js');

$settings=HSettings::getById('accounts');
$account=new Account('restore_password_change');

?>
<script>function js:restoreformv(form, data, hasError){
let af=new kontur_activeform(form, ".popup-form-item", "");let fields=$(form).find("select,textarea,input:not(:submit):not(:button):not([type=radio]):not([type=checkbox]),input:checked");
fields.attr('title', '');if(!hasError){$.post('/accounts/auth/ajaxRestorePassword',kontur_form2object(fields),function(r){
    	if(r.success === true) {$('#<?=$this->id?>').addClass('successed').html(r.data.text + '<div style="margin-top:40px;text-align:center;width:100%"><a href="javascript:;" class="btn" style="margin:0 auto" onclick="$.fancybox.close();">Закрыть</a></div>');}
    	else {af.modelPrefix='crud_models_ar_accounts_models_Account_';af.errorResponce(r.errors);af.modelPrefix='';}
},'json');}else{af.errorResponce(data);}return false;}
$(document).on('click', '.js-btn-login', function(e){$.fancybox.getInstance('close');$.fancybox.open({src:'#modal-loginform', type:'inline'});});
</script>
<?php if($this->popup): ?><div style="display: none;"><?php endif; ?>
	<div class="fancybox-content" id="<?=$this->id?>">
    	<div class="reg-form-box reg-form-box-restore">
            <h3>Восстановление пароля</h3>
            <span><?= $settings->restore_form_text; ?></span>
            <?php $this->widget('\common\widgets\form\ActiveForm', [
                'id'=>'accounts__restore-form',
                'model'=>$account,
                'attributes'=>['email'],
                'tag'=>false,
        	    'errorSummary'=>false,
        	    'submitLabel'=>function() {
        	       ?><div class="send-btn">
        	       		<input type="submit" value="Продолжить">
        	       	</div><?php 
        	    },
        	    'formOptions'=>[
        	        'enableAjaxValidation'=>true,
        	        'enableClientValidation'=>false,
        	        'clientOptions'=>[
        	            'hideErrorMessage'=>false,
        	            'validationUrl'=>'/accounts/auth/ajaxRestorePassword',
        	            'afterValidate'=>'js:restoreformv'
        	        ],
        	        'htmlOptions'=>['class'=>'reg-form']
        	    ],
        	    'htmlOptions'=>[
        	        'rowTag'=>'div',
        	        'rowOptions'=>['class'=>'popup-form-item']
        	    ]
            ]); ?>
        </div>
	</div>	
<?php if($this->popup): ?></div><?php endif; ?>