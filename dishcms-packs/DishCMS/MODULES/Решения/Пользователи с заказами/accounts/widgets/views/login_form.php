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
$(document).on('click', '.js-btn-reg', function(e){$.fancybox.getInstance('close');$.fancybox.open({src:'#modal-regform', type:'inline'});});
EOL;
 
Y::js('accounts_login_form', $loginFormJsCode, \CClientScript::POS_READY); 
?>
<?php if($this->popup): ?><div style="display: none;"><?php endif; ?>
	<div class="fancybox-content<? if($this->popup): ?> fancybox-content-popup<? endif; ?>" id="<?=$this->id?>">
    	<div class="reg-form-box">
            <h3>Вход</h3>
            <span><?= $settings->signin_form_text; ?></span>
        	<?php $this->widget('\common\widgets\form\ActiveForm', [
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
                        <a href="javascript:;" class="form-links js-btn-reg">Я хочу зарегистрироваться</a>
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

<?php $this->owner->widget('\accounts\widgets\RegForm', ['id'=>'modal-regform', 'popup'=>true]); ?>

