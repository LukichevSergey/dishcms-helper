<?php
/** @var \accounts\widgets\RegForm $this */
use common\components\helpers\HYii as Y;
use settings\components\helpers\HSettings;
use crud\models\ar\accounts\models\Account;
use crud\models\ar\accounts\models\Country;

Y::module('common')->publishJs('js/tools/activeform.js');
Y::module('common')->publishJs('js/tools/form2object.js');

$settings=HSettings::getById('accounts');
$account=new Account('registration');
?>
<script>function regformv(form, data, hasError){
let af=new kontur_activeform(form, ".popup-form-item", "");let fields=$(form).find("select,textarea,input:not(:submit):not(:button):not([type=radio]):not([type=checkbox]),input:checked");
fields.attr('title', '');if(!hasError){$.post('/accounts/reg/ajaxRegistration',kontur_form2object(fields),function(r){
    	if(r.success === true) {$('#<?=$this->id?>').addClass('successed').html(r.data.text + '<div style="margin-top:40px;text-align:center;width:100%"><a href="javascript:;" class="btn" style="margin:0 auto" onclick="$.fancybox.close();">Close</a></div>');}
    	else {af.modelPrefix='crud_models_ar_accounts_models_Account_';af.errorResponce(r.errors);af.modelPrefix='';}
},'json');}else{af.errorResponce(data);}return false;}</script>
<div style="display: none;">
	<div class="popup-form register-form" id="<?=$this->id?>">
	<?php $this->beginWidget('\common\widgets\form\ActiveForm', [
	    'id'=>'accounts__reg-form',
	    'model'=>$account,
	    'attributes'=>['category', 'name', 'password', 'email', 'country_id', 'company', 'phone'],
	    'tag'=>false,
	    'errorSummary'=>false,
	    'formOptions'=>[
	        'enableAjaxValidation'=>true,
	        'enableClientValidation'=>false,
	        'clientOptions'=>[
	            'hideErrorMessage'=>false,
	            'validationUrl'=>'/accounts/reg/ajaxRegistration',
	            'afterValidate'=>'js:regformv'
	        ]
	    ],
	    'types'=>[
	        'category'=>function($widget, $form, $attribute) {
	             ?>
	             <div class="popup-form-body">
    	         <div class="popup-form-item popup-form-radio">
    	        	<div class="popup-form-radio-title">CATEGORY</div>
	        		<div class="popup-form-radio-group">
	        			<?= $form->radioButtonList($widget->model, $attribute, $widget->model->categoryLabels(), [
	        			    'template'=>'<div class="popup-form-radio-item">{input}{label}</div>',
	        			    'separator'=>'',
	        			    'container'=>'',
	        			]); ?>
	        			<?= $form->error($widget->model, $attribute); ?>
    	        	</div>
    	        </div>
    	        <?php
	        },
	        'name'=>function($widget, $form, $attribute) {
	            ?>
	            <div class="popup-form-item">
                	<label for="">User name:</label>
                	<?= $form->textField($widget->model, $attribute, ['placeholder'=>'Your public profile name']); ?>
    			  <?= $form->error($widget->model, $attribute); ?>
                </div>
	            <?php 
	        },
	        'password'=>function($widget, $form, $attribute) {
	            ?>
	            <div class="popup-form-item">
          			<label for="">Password:</label>
          			<?= $form->passwordField($widget->model, $attribute, ['placeholder'=>'6 characteers or more']); ?>
	        		<?= $form->error($widget->model, $attribute); ?>
		        </div>
	        	<?php 
	        },
	        'email'=>function($widget, $form, $attribute) {
	            ?>
				<div class="popup-form-item">
          			<label for="">E-mail:</label>
          			<?= $form->textField($widget->model, $attribute, ['placeholder'=>'Your email address']); ?>
	        		<?= $form->error($widget->model, $attribute); ?>
        		</div>
	        	<?php 
	        },
	        'country_id'=>function($widget, $form, $attribute) {
                ?>
	            <div class="popup-form-item">
					<label for="">Country</label>
					<?= $form->dropDownList($widget->model, $attribute, Country::model()->published()->bySort()->listData('title'), ['class'=>'js-regform-region']); ?>
	        		<?= $form->error($widget->model, $attribute); ?>          
        		</div>
	        	<?php 
	        	//Y::js(false, '$(document).on("change",".js-regform-region",function(e){let p=$(".js-regform-phone");p.intlTelInput("setCountry", $(e.target).val());});', \CClientScript::POS_READY);
	        },
	        'company'=>function($widget, $form, $attribute) {
	            ?>
	           	<div class="popup-form-item">
          	   		<label for="">Company name</label>
          	   		<?= $form->textField($widget->model, $attribute, ['placeholder'=>'Company name']); ?>
	        		<?= $form->error($widget->model, $attribute); ?>
        		</div>
	        	<?php 
	        },
	        'phone'=>function($widget, $form, $attribute) {
	            if(!$widget->model->phone_country){$widget->model->phone_country='us';}
	            ?>
	            <div class="popup-form-item">
          	   		<label for="">Phone Number</label>
          	   		<?php $this->widget('\common\widgets\form\I18nPhoneField', [
          	   		    'form'=>$form,
          	   		    'model'=>$widget->model,
          	   		    'attribute'=>$attribute,
          	   		    'hideLabel'=>true,
          	   		    'tag'=>false,
                        'attributeMask'=>'phone_mask',
                        'attributeCountry'=>'phone_country',
                        'attributeCountryCode'=>'phone_country_code',
                        'options'=>[
                            'preferredCountries'=>array_keys($widget->model->getCountryPreferrerLabels()),
                            'onlyCountries'=>array_keys($widget->model->getCountryLabels())                            
          	   		    ],
          	   		    'htmlOptions'=>['placeholder'=>'Phone number must have a country code', 'class'=>'js-regform-phone']
          	   		]); ?>
        		</div>
	        	<?php
	        	//Y::js(false, '$(document).on("countrychange",".js-regform-phone",function(e){$(".js-regform-country").val($(e.target).intlTelInput("getSelectedCountryData").iso2);$(".js-regform-country").trigger("change");});', \CClientScript::POS_READY);
	        },	        
	    ],
	    'privacyLabel'=>function($widget, $form) use ($settings) {
	        ?>
	        </div>
	        <div class="popup-form-privacy">
	        	<?= $form->checkBox($widget->model, 'privacy'); ?>
	        	<?= $form->labelEx($widget->model, 'privacy', [
	        	    'encode'=>false, 
	        	    'required'=>false,
	        	    'label'=>'By clicking the button below, I agree with ' . \CHtml::link('Terms of Service', $settings->terms_link) 
	        	          . ' and ' . \CHtml::link('Privacy Policy', $settings->privacy_link) . '.'
	        	]); ?>
                <?= $form->error($widget->model, 'privacy'); ?>
            </div>
            <?php
	    },
	    'submitLabel'=>function() {
	        echo \CHtml::tag('div', ['class'=>'popup-form-submit'], \CHtml::tag('button', ['class'=>'btn', 'type'=>'submit'], 'Register'));
	    },
	]); ?>
    #BEGINFORM#
        <div class="popup-form-title">Register now</div>
        <div class="popup-form-subtitle"><?= $settings->reg_form_text; ?></div>          
		#FORM#
    #ENDFORM#
	<?php $this->endWidget(); ?>
	</div>	
</div>