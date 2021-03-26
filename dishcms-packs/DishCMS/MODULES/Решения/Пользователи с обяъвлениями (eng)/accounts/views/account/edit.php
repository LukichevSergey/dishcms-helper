<?php
/** @var \accounts\controllers\AccountController $this */
/** @var \crud\models\ar\accounts\models\Account $account */
use common\components\helpers\HYii as Y;
use accounts\components\helpers\HAccount;
use crud\models\ar\accounts\models\Country;

Y::js('edit-profile', '(function(){let sel=$(".form-advert select"),intervalId=setInterval(function(){if(typeof sel.select2!="undefined"){
sel.select2({minimumResultsForSearch: -1,dropdownCssClass:"popup-form-dropdown"});clearInterval(intervalId);}},50);})();', \CClientScript::POS_READY);
?>

<div class="account-main account-wanted">
	<div class="account-main-head">
		<h2>Edit personal information</h2>
	</div>
	<?php $this->widget('\accounts\widgets\FlashMessage'); ?>
	<div class="form-advert">
		<?php $this->beginWidget('\common\widgets\form\ActiveForm', [
    	    'id'=>'accounts__edit-profile-form',
    	    'model'=>$account,
    	    'attributes'=>['company', 'category', 'country_id', 'name', 'phone', 'email', 'company_logo'],
    	    'tag'=>false,
    	    'errorSummary'=>false,
    	    'formOptions'=>[
    	        'enableAjaxValidation'=>true,
    	        'enableClientValidation'=>false,
    	        'clientOptions'=>[
    	            'hideErrorMessage'=>false,
    	            'validationUrl'=>'/accounts/account/edit',
    	        ],
    	        'htmlOptions'=>['enctype'=>'multipart/form-data']
    	    ],
    	    'types'=>[
    	        'company'=>function($widget, $form, $attribute) {
    	            ?>
        	        <div class="form-advert-body">
        	        	<div class="form-advert-item">
        	        		<label for="">Company Name:</label>
        	        		<?= $form->textField($widget->model, $attribute, ['placeholder'=>'Here your company name']); ?>
        			  		<?= $form->error($widget->model, $attribute); ?>        	        
    	        		</div>
    	        	<?php 
    	        },
    	        'category'=>function($widget, $form, $attribute) {
    	            ?>
    	        	<div class="form-advert-item">
    	        		<label for="">Category:</label>
    	        		<?= $widget->model->getCategoryLabel(); ?>        	        
	        		</div>
    	        	<?php 
    	        },
    	        'country_id'=>function($widget, $form, $attribute) {
    	            ?>
    	        	<div class="form-advert-item">
    	        		<label for="">Country:</label>
    	        		<?= $form->dropDownList($widget->model, $attribute, Country::model()->published()->bySort()->listData('title', ['order'=>'region_id, title', 'select'=>'id, title, region_id'], null, 'id', function($country) {
                                    return $country->region->title;
                        }), ['empty'=>'-- Please choose your country --']); ?>
    			  		<?= $form->error($widget->model, $attribute); ?>        	        
	        		</div>
    	        	<?php 
    	        },
    	        'name'=>function($widget, $form, $attribute) {
    	            ?>
    	        	<div class="form-advert-item">
    	        		<label for="">Contact person:</label>
    	        		<?= $form->textField($widget->model, $attribute, ['placeholder'=>'Here your contact person']); ?>
    			  		<?= $form->error($widget->model, $attribute); ?>        	        
	        		</div>
    	        	<?php 
    	        },
    	        'phone'=>function($widget, $form, $attribute) {
    	            ?>
    	            <div class="form-advert-item">
                    	<label for="">Phone number:</label>
                    	<?php $this->widget('\common\widgets\form\I18nPhoneField', [
                            'form'=>$form,
                    	    'model'=>$widget->model,
    					    'attribute'=>'phone',
                            'attributeMask'=>'phone_mask',
                            'attributeCountry'=>'phone_country',
                            'attributeCountryCode'=>'phone_country_code',
    					    'hideLabel'=>true,
                            'options'=>[
                                'preferredCountries'=>array_keys($widget->model->getCountryPreferrerLabels()),
                                'onlyCountries'=>array_keys($widget->model->getCountryLabels())                            
                            ],
    					    'tag'=>false,
    					    'htmlOptions'=>['style'=>'width:100%']
    					]); ?>
        			    <?= $form->error($widget->model, $attribute); ?>
                    </div>
    	            <?php 
    	        },
    	        'email'=>function($widget, $form, $attribute) {
    	            ?>
        	            <div class="form-advert-item">
                        	<label for=""><b>E-mail</b></label>
                        	<?= $form->textField($widget->model, $attribute, ['placeholder'=>'Your email address']); ?>
            			    <?= $form->error($widget->model, $attribute); ?>
                        </div>
                    </div>
    	            <?php 
    	        },
    	        'company_logo'=>function($widget, $form, $attribute) {
    	            ?>
    	            <div class="form-advert-footer">
        				<div for="upload-doc" class="doc-block">
        					<?= $form->fileField($widget->model, $widget->model->companyLogoBehavior->attributeFile, ['id'=>'upload-doc', 'class'=>'form-advert-upload']); ?>
        					<label for="upload-doc">
        						<i class="doc-icon">
        							<img src="/images/add-doc.svg">
        						</i>
        						<span>Add company logo</span>
        					</label>
        					<div class="errorMessage js-upload-doc-error"></div>
        					<?php if($widget->model->companyLogoBehavior->exists()) { ?>
        					<input type="hidden" name="account_delete_company_logo" value="0" />
        					<div class="form-advert-upload-image js-upload-doc-image">
        						<img src="<?php echo $widget->model->companyLogoBehavior->getSrc(); ?>" />
        						<a href="javascript:;" class="js-upload-doc-image-remove">delete</a>
        					</div>
        					<?php } else { ?>
        					<div class="form-advert-upload-image js-upload-doc-image" style="display:none">
        						<img src="" />
        						<a href="javascript:;" class="js-upload-doc-image-remove">delete</a>
        					</div>
        					<?php } ?>
        					<script>
        					$(document).on('change', '#upload-doc', function(e) {
            					let file=$(e.target),err=$('.js-upload-doc-error'),img=$('.js-upload-doc-image');
            					err.hide();
            					if(file[0].files.length>0) {
                					if($.inArray(file[0].files[0].type, ['image/jpeg', 'image/png']) < 0) {
            			            	err.text('Only PNG and JPG files are allowed for download.').show();
            			            	file.val('');img.hide();
                					}
                					else {
                						var reader=new FileReader();
                					    reader.onload=function(){
                					    	img.find('img').attr('src', reader.result);
                					    	img.show();
                					    };
                					    reader.readAsDataURL(file[0].files[0]);
                					}
            					}
            					else { img.hide(); }
        			        });
        					$(document).on('click','.js-upload-doc-image-remove',function(e){
            					$('#upload-doc').val('');$('.js-upload-doc-image').hide();
            					if($('[name=account_delete_company_logo]').length){$('[name=account_delete_company_logo]').val(1);}
            				});
        					</script>
        				</div>
    	        	<?php 
    	        },
    	    ],
    	    'submitLabel'=>function() {
    	        ?>
    	        	<button type="submit" class="btn">Save</button>
				</div>
    	        <?php 
    	    },
    	]); ?>
        #BEGINFORM#
            <h3>For Sale</h3>          
    		#FORM#
        #ENDFORM#
	<?php $this->endWidget(); ?>
	</div>
</div>