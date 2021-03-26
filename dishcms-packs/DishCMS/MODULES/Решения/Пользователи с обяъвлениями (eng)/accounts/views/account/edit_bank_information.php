<?php
/** @var \accounts\controllers\AccountController $this */
/** @var \crud\models\ar\accounts\models\Account $account */

?>
<div class="account-main account-wanted">
	<div class="account-main-head">
		<h2>Edit bank information</h2>
	</div>
	<?php $this->widget('\accounts\widgets\FlashMessage'); ?>
	<div class="form-advert">
		<?php $this->beginWidget('\common\widgets\form\ActiveForm', [
    	    'id'=>'accounts__edit-bank-information-form',
    	    'model'=>$account,
    	    'attributes'=>['bank_info'],
    	    'tag'=>false,
    	    'errorSummary'=>false,
    	    'formOptions'=>[
    	        'enableAjaxValidation'=>true,
    	        'enableClientValidation'=>false,
    	        'clientOptions'=>[
    	            'hideErrorMessage'=>false,
    	            'validationUrl'=>'/accounts/account/editBankInformation',
    	        ],
    	    ],
    	    'types'=>[
    	        'bank_info'=>function($widget, $form, $attribute) {
    	            ?>
        	        <div class="form-advert-body">
        	        	<div class="form-advert-item">
        	        		<? $this->widget('\common\ext\dataAttribute\widgets\DataAttribute', [
                        		'behavior' => $widget->model->bankInfoBehavior,
                        		'header'=>['title'=>'Label', 'value'=>'Text'],
        	        		    'types'=>[
        	        		        'title'=>'text',
        	        		        'value'=>'text'
        	        		    ],
        	        		    'defaultActive'=>true,
        	        		    'hideActive'=>true,
                        		'default' =>[
                        			['title'=>'Bank name', 'value'=>''],
                        		],
        	        		    'view'=>'account_info'
                        	]); ?>    	        
    	        		</div>
    	        	</div>
    	        	<?php 
    	        },
            ],
    	    'submitLabel'=>function() {
    	        ?>
    	        <div class="form-advert-footer">
    	        	<button type="submit" class="btn">Save</button>
				</div>
    	        <?php 
    	    },
    	]); ?>
        #BEGINFORM#
            <h3>Bank Information</h3>          
    		#FORM#
        #ENDFORM#
	<?php $this->endWidget(); ?>
	</div>
</div>