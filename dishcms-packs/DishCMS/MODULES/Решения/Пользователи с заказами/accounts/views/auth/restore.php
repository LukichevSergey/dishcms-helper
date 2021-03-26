<?php
/** @var \accounts\controllers\AuthController $this */
/** @var \crud\models\ar\accounts\models\Account $account */
use settings\components\helpers\HSettings;

$settings=HSettings::getById('accounts');
?>
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