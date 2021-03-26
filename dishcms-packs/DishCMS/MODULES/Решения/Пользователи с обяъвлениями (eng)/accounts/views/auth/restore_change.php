<?php
/** @var \accounts\controllers\AuthController $this */
/** @var \crud\models\ar\accounts\models\Account $account */

?>
<div class="popup-form register-form auth-form" id="<?=$this->id?>">
	<?php $this->beginWidget('\common\widgets\form\ActiveForm', [
        'id'=>'accounts__restore-form',
        'model'=>$account,
        'attributes'=>['password', 'repassword'],
	    'types'=>[
	        'password'=>function($widget, $form, $attribute) {
	            ?>
	            <div class="popup-form-body">
    	            <div class="popup-form-item">
                    	<label for="">New password:</label>
                    	<?= $form->passwordField($widget->model, $attribute, ['placeholder'=>'Your new password']); ?>
        			    <?= $form->error($widget->model, $attribute); ?>
                    </div>
	            <?php 
	        },
	        'repassword'=>function($widget, $form, $attribute) {
	            ?>
    	            <div class="popup-form-item">
                    	<label for="">Repeat new password:</label>
                    	<?= $form->passwordField($widget->model, $attribute, ['placeholder'=>'Repeat your new password']); ?>
        			    <?= $form->error($widget->model, $attribute); ?>
                    </div>
                </div>
	            <?php 
	        },
        ],
        'tag'=>false,
	    'errorSummary'=>false,
	    'submitLabel'=>function() {
	        echo \CHtml::tag('div', ['class'=>'popup-form-submit'], \CHtml::tag('button', ['class'=>'btn', 'type'=>'submit'], 'Save'));
	    },
	    'formOptions'=>[
	        'enableAjaxValidation'=>true,
	        'enableClientValidation'=>false,	        
	    ]
    ]); ?>
    #BEGINFORM#
        <div class="popup-form-title">Restore Password</div>          
		#FORM#
    #ENDFORM#
	<?php $this->endWidget(); ?>	
</div>