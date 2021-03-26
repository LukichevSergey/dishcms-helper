<?php
/** @var \accounts\controllers\AuthController $this */
/** @var \crud\models\ar\accounts\models\Account $account */
use common\components\helpers\HYii as Y;
use settings\components\helpers\HSettings;
use crud\models\ar\accounts\models\Account;

Y::module('common')->publishJs('js/tools/activeform.js');
Y::module('common')->publishJs('js/tools/form2object.js');

$settings=HSettings::getById('accounts');
$account=new Account('registration');

$loginFormJsCode=<<<'EOL'
function loginformv2(form, data, hasError){
let af=new kontur_activeform(form, ".popup-form-item", "");let fields=$(form).find("select,textarea,input:not(:submit):not(:button):not([type=radio]):not([type=checkbox]),input:checked");
fields.attr('title', '');if(!hasError){$.post('/accounts/auth/ajaxLogin',kontur_form2object(fields),function(r){
if(r.success === true) {if(r.data && !isNaN(+r.data.admin) && (+r.data.admin === 1)){window.location.href='/cp/default/index';}else{window.location.href='/accounts/account';}}
else {af.modelPrefix='crud_models_ar_accounts_models_Account_';af.errorResponce(r.errors);af.modelPrefix='';}
},'json');}else{af.errorResponce(data);}return false;}
EOL;
 
Y::js('accounts_login_form2', $loginFormJsCode, \CClientScript::POS_READY); 
?>
<div class="reg-form-box reg-form-box-page">
    <h3>Вход в личный кабинет</h3>
    <span><?= $settings->signin_form_text; ?></span>
	<?php $this->widget('\common\widgets\form\ActiveForm', [
	    'id'=>'accounts__auth-form2',
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
	            'afterValidate'=>'js:loginformv2'
	        ],
	        'htmlOptions'=>['class'=>'reg-form']
	    ],
	    'types'=>[
	        'password'=>'passwordField',
	        'remember_me'=>function($widget, $form, $attribute) {
	            ?>
	            <div class="confirmation gray">
                    <label class="check">
                    	<?= $form->checkBox($widget->model, $attribute, ['class'=>'input-default']); ?>
                        <span class="input-custom"></span>
                        Запомнить меня
    	        	</label>
                    <?= $form->error($widget->model, $attribute); ?>
                </div>
	        	<?php 
	        }
	    ],
	    'submitLabel'=>function() {
	        ?>
	        <div class="form-links-box form-link-fogot">
                <a href="/accounts/auth/restore" class="form-links">Забыли пароль?</a>
            </div>
            <div class="send-btn">
                <input type="submit" value="Войти">
            </div>
	        <div class="form-links-box form-link-reg">
                <a href="javascript:;" data-fancybox data-src="#modal-regform" class="form-links">Я хочу зарегистрироваться</a>
            </div>
	        <?php 
	    },
	    'htmlOptions'=>[
	        'rowTag'=>'div',
	        'rowOptions'=>['class'=>'popup-form-item']
	    ]
	]); ?>
</div>