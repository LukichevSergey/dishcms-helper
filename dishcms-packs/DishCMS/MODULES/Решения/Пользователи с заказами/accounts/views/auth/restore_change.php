<?php
/** @var \accounts\controllers\AuthController $this */
/** @var \crud\models\ar\accounts\models\Account $account */
use settings\components\helpers\HSettings;

$settings=HSettings::getById('accounts');
?>

<div class="fancybox-content" id="<?=$this->id?>">
	<div class="reg-form-box reg-form-box-restore">
        <h3>Восстановление пароля</h3>
        <span><?= $settings->restore_change_form_text; ?></span>
        <?php $this->widget('\common\widgets\form\ActiveForm', [
            'id'=>'accounts__restore-form',
            'model'=>$account,
            'attributes'=>['password', 'repassword'],
    	    'types'=>[
    	        'password'=>'passwordField',
    	        'repassword'=>'passwordField',
    	    ],
    	    'formOptions'=>[
    	        'enableAjaxValidation'=>true,
    	        'enableClientValidation'=>false,
    	        'clientOptions'=>[
    	            'hideErrorMessage'=>false,
    	        ],
    	        'htmlOptions'=>['class'=>'reg-form']
    	    ],
            'tag'=>false,
    	    'errorSummary'=>false,
    	    'submitLabel'=>function() {
    	       ?><div class="send-btn">
    	       		<input type="submit" value="Сохранить">
    	       	</div><?php 
    	    },
    	    'htmlOptions'=>[
    	        'rowTag'=>'div',
    	        'rowOptions'=>['class'=>'popup-form-item']
    	    ]
        ]); ?>
    </div>
</div>