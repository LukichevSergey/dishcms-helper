<?php
/** @var \accounts\widgets\LoginForm $this */
use common\components\helpers\HYii as Y;
use settings\components\helpers\HSettings;
use crud\models\ar\accounts\models\Account;
use crud\models\ar\accounts\models\Region;

Y::module('common')->publishJs('js/tools/activeform.js');
Y::module('common')->publishJs('js/tools/form2object.js');

$settings=HSettings::getById('accounts');
$account=new Account('registration');

$loginFormJsCode=<<<'EOL'
function loginformv(form, data, hasError){
let af=new kontur_activeform(form, ".popup-form-item", "");let fields=$(form).find("select,textarea,input:not(:submit):not(:button):not([type=radio]):not([type=checkbox]),input:checked");
fields.attr('title', '');if(!hasError){$.post('/accounts/auth/ajaxLogin',kontur_form2object(fields),function(r){
if(r.success === true) { if(r.data && !isNaN(+r.data.admin) && (+r.data.admin === 1)){window.location.href='/cp/default/index';}else{window.location.href='/accounts/account';}}
else {af.modelPrefix='crud_models_ar_accounts_models_Account_';af.errorResponce(r.errors);af.modelPrefix='';}
},'json');}else{af.errorResponce(data);}return false;}
EOL;
 
Y::js('accounts_login_form', $loginFormJsCode, \CClientScript::POS_READY); 
?>
<div style="display: none;">
	<div class="popup-form register-form auth-form" id="<?=$this->id?>">
	<?php $this->beginWidget('\common\widgets\form\ActiveForm', [
	    'id'=>'accounts__auth-form',
	    'model'=>$account,
	    'attributes'=>['email', 'password', 'remember_me'],
	    'tag'=>false,
	    'errorSummary'=>false,
	    'formOptions'=>[
	        'enableAjaxValidation'=>true,
	        'enableClientValidation'=>false,
	        'clientOptions'=>[
	            'hideErrorMessage'=>false,
	            'validationUrl'=>'/accounts/auth/ajaxLogin',
	            'afterValidate'=>'js:loginformv'
	        ]
	    ],
	    'types'=>[
	        'email'=>function($widget, $form, $attribute) {
	            ?>
	            <div class="popup-form-body">
	            <div class="popup-form-item">
                	<label for="">User name:</label>
                	<?= $form->textField($widget->model, $attribute, ['placeholder'=>'Your email address']); ?>
    			  <?= $form->error($widget->model, $attribute); ?>
                </div>
	            <?php 
	        },
	        'password'=>function($widget, $form, $attribute) {
	            ?>
	            <div class="popup-form-item">
          			<label for="">Password:</label>
          			<?= $form->passwordField($widget->model, $attribute, ['placeholder'=>'Your password']); ?>
	        		<?= $form->error($widget->model, $attribute); ?>
		        </div>
		        </div>
	        	<?php 
	        },
	        'remember_me'=>function($widget, $form, $attribute) {
	            ?>
    	        <div class="popup-form-privacy">
    	        	<?= $form->checkBox($widget->model, $attribute); ?>
    	        	<?= $form->labelEx($widget->model, $attribute, [
    	        	    'encode'=>false, 
    	        	    'required'=>false,
    	        	    'label'=>'Remember me on this site.'
    	        	]); ?>
                    <?= $form->error($widget->model, $attribute); ?>
                </div>
	        	<?php 
	        }
	    ],
	    'submitLabel'=>function() {
	        echo \CHtml::tag('div', ['class'=>'popup-form-submit'], \CHtml::tag('button', ['class'=>'btn', 'type'=>'submit'], 'Enter'));
	    },
	]); ?>
    #BEGINFORM#
        <div class="popup-form-title">Sign In</div>
        <div class="popup-form-subtitle"><?= $settings->signin_form_text; ?></div>          
		#FORM#
    #ENDFORM#
    <a href="/accounts/auth/restore" class="accounts__auth_restore-link">Forgot your password?</a>
	<?php $this->endWidget(); ?>
	</div>	
</div>