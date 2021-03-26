<?php
/** @var \accounts\controllers\AccountController $this */
/** @var \crud\models\ar\accounts\models\Advert $advert */
use common\components\helpers\HYii as Y;

$advertDetailTypes=$advert->getAdvertDetailTypeList(true);
if(count($advertDetailTypes) < 1):
    Y::css('flash_message-fail', '.accounts__flash-fail{width:100%;border:1px solid #e68b84;border-radius:3px;padding:5px 10px;text-align:center;color:#ca3e34;background:#f8dfdf;}');
    ?>
    <div class="account-main account-wanted">
    	<div class="account-main-head">
    		<h2>Edit Advert</h2>
    	</div>
    	<div class="accounts__flash-fail">You are not authorized to edit ads.</div>
    </div>
<?php 
else: 
?>
<div class="account-main account-wanted">
	<div class="account-main-head">
		<h2>Edit Advert</h2>
	</div>
	<?php $this->widget('\accounts\widgets\FlashMessage'); ?>
	<div class="form-advert">
		<?php $this->beginWidget('\common\widgets\form\ActiveForm', [
    	    'id'=>'accounts__edit-advert-form',
		    'model'=>$advert,
		    'attributes'=>['part_number', 'part_type', 'quantity', 'code', 'more_info', 'detail_type', 'category', 'file'],
    	    'tag'=>false,
    	    'errorSummary'=>false,
    	    'formOptions'=>[
    	        'enableAjaxValidation'=>true,
    	        'enableClientValidation'=>false,
    	        'clientOptions'=>[
    	            'hideErrorMessage'=>false,
    	            'validationUrl'=>'/accounts/account/advertEdit/' . $advert->id,
    	        ],
    	        'htmlOptions'=>['enctype'=>'multipart/form-data']
    	    ],
    	    'types'=>[
    	        'part_number'=>function($widget, $form, $attribute) {
    	            ?>
        	        <div class="form-advert-body">
        	        	<div class="form-advert-item">
        	        		<label for="">Looking for Part Number:</label>
        	        		<?= $form->textField($widget->model, $attribute, ['placeholder'=>'Here your Part Number']); ?>
        			  		<?= $form->error($widget->model, $attribute); ?>        	        
    	        		</div>
    	        	<?php 
    	        },
    	        'part_type'=>function($widget, $form, $attribute) {
    	            ?>
    	        	<div class="form-advert-item<?php /* ?> form-advert-type<? /**/ ?>">
    	        		<label for="">Type of part:</label>
    	        		<?= $form->textField($widget->model, $attribute, ['placeholder'=>'Add type of part']); ?>
    			  		<?= $form->error($widget->model, $attribute); ?>        	        
	        		</div>
    	        	<?php 
    	        },
    	        'quantity'=>function($widget, $form, $attribute) {
        	        if(!$widget->model->$attribute) {
        	            $widget->model->$attribute=null;
        	        }
    	            ?>
    	        	<div class="form-advert-item<?php /* ?> form-advert-quantity<? /**/ ?>">
    	        		<label for="">Quantity:</label>
    	        		<?= $form->textField($widget->model, $attribute, ['placeholder'=>'1']); ?>
    			  		<?= $form->error($widget->model, $attribute); ?>        	        
	        		</div>
    	        	<?php 
    	        },
    	        'code'=>function($widget, $form, $attribute) {
    	            ?>
    	        	<div class="form-advert-item">
    	        		<label for="">Condition / Capability Code:</label>
    	        		<?= $form->textField($widget->model, $attribute, ['placeholder'=>'NE']); ?>
    			  		<?= $form->error($widget->model, $attribute); ?>        	        
	        		</div>
    	        	<?php 
    	        },
    	        'more_info'=>function($widget, $form, $attribute) {
    	            ?>
    	        	<div class="form-advert-item form-advert-item-information">
    					<label for=""></label>
    					<?= $form->textArea($widget->model, $attribute, ['placeholder'=>'More information']); ?>
    			  		<?= $form->error($widget->model, $attribute); ?>
    				</div>
    	        	<?php 
    	        },
    	        'detail_type'=>function($widget, $form, $attribute) use ($advertDetailTypes) {
    	            if(!$widget->model->$attribute) {
    	                $widget->model->$attribute=key($advertDetailTypes);
    	            }
    	            ?>
    	            <div class="form-advert-item">
    	            	<?php if(count($advertDetailTypes) > 1): ?>
                    		<label for="">
                    		<?= $form->radioButtonList($widget->model, $attribute, $advertDetailTypes, ['separator'=>'<b>or</b><br>']); ?>
                    		<?= $form->error($widget->model, $attribute); ?>    
                    		:</label>
                    	<?php else: $widget->model->$attribute=key($advertDetailTypes); ?>
                    		<label for=""><?= reset($advertDetailTypes); ?>:</label>
                    		<?= $form->hiddenField($widget->model, $attribute); ?>
                    	<?php endif; ?>
                    	<?= $form->textField($widget->model, 'detail_type_value', ['placeholder'=>'']); ?>
    			  		<?= $form->error($widget->model, 'detail_type_value'); ?>        
                    </div>
    	            <?php 
    	        },
    	        'category'=>function($widget, $form, $attribute) {
    	            ?>
            	        <div class="form-advert-item">
                        	<label for="">Category:</label>
                        	<?= $form->textField($widget->model, $attribute, ['placeholder'=>'Other']); ?>
        			  		<?= $form->error($widget->model, $attribute); ?>        
                        </div>
                    </div>
    	            <?php 
    	        },
    	        'file'=>function($widget, $form, $attribute) {
    	            ?>
    	            <div class="form-advert-footer">
        				<div for="upload-doc" class="doc-block">
        					<?= $form->fileField($widget->model, $widget->model->fileBehavior->attributeFile, ['id'=>'upload-doc', 'class'=>'form-advert-upload']); ?>
        					<label for="upload-doc">
        						<i class="doc-icon">
        							<img src="/images/add-doc.svg">
        						</i>
        						<span>Add document</span>
        					</label>
        					<div class="errorMessage js-upload-doc-error"></div>
        					<?php if($widget->model->fileBehavior->exists()) { ?>
            					<input type="hidden" name="advert_delete_file" value="0" />
            					<div class="form-advert-file js-upload-doc-file">
            						<?= $widget->model->getAdvertFileDownloadLink(['class'=>'form-advert-file-name uploaded-file']); ?>
            						<a href="javascript:;" class="js-upload-doc-file-remove">delete</a>
            					</div>
        					<?php } else { ?>
        					<div class="form-advert-upload-image js-upload-doc-file" style="display:none">
        						<a class="form-advert-file-name" href="javascript:;"></a>
        						<a href="javascript:;" class="js-upload-doc-file-remove">delete</a>
        					</div>
        					<?php } ?>
        					<script>
        					$(document).on('change', '#upload-doc', function(e) {
            					let file=$(e.target),err=$('.js-upload-doc-error'),ff=$('.js-upload-doc-file');
            					err.hide();
            					if(file[0].files.length>0) {
                					let a=ff.find('a:not(.js-upload-doc-file-remove)');
                					a.removeClass('uploaded-file');
                					a.attr('href', 'javascript:;');
                					a.text(file[0].files[0].name);
                					ff.show();
            					}
            					else { ff.hide(); }
        			        });
        					$(document).on('click','.js-upload-doc-file-remove',function(e){
            					$('#upload-doc').val('');$('.js-upload-doc-file').hide();
            					if($('[name=advert_delete_file]').length){$('[name=advert_delete_file]').val(1);}
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
            <h3>Advert #<?= $advert->id; ?></h3>          
    		#FORM#
        #ENDFORM#
	<?php $this->endWidget(); ?>
	</div>
</div>
<?php endif; ?>