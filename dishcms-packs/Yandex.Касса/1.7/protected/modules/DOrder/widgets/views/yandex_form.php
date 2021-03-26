<?php
/** @var \DOrder\widgets\YandexFormWidget $this */
?>
<div class="form" style="width: 60%">
    <?php $form = $this->beginWidget('CActiveForm', array(
        'id'=>'dorder-yandex-form',
        'enableClientValidation'=>true,
        'clientOptions'=>array(
            'validateOnSubmit'=>true,
            'validateOnChange'=>false,
        	'beforeValidate'=>'js:function(form) { $(form).find(\'.ym-form-submit-btn \').attr(\'disabled\', true); return true; }',
        	'afterValidate'=>'js:function(form, data, hasError) { if(hasError) $(form).find(\'.ym-form-submit-btn \').attr(\'disabled\', false); else form.submit(); return true; }',
        ),
    )); /* @var CActiveForm $form */ ?>
    <?php echo $form->hiddenField($this->model, 'scid'); ?>
    <?php echo $form->hiddenField($this->model, 'ShopID'); ?>

    <div class="row">
        <?php echo $form->labelEx($this->model, 'CustName'); ?>
        <?php echo $form->textField($this->model, 'CustName'); ?>
        <?php echo $form->error($this->model, 'CustName'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($this->model, 'CustEMail'); ?>
        <?php echo $form->textField($this->model, 'CustEMail'); ?>
        <?php echo $form->error($this->model, 'CustEMail'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($this->model, 'phone'); ?>
        <?php
	        $this->widget('CMaskedTextField', array(
	            'model' => $this->model,
	            'attribute' => 'phone',
	            'mask' => '+7 ( 999 ) 999 - 99 - 99',
				'htmlOptions' => array('placeholder'=>'+7 ( ___ ) ___ - __ - __')
	        ));
	    ?>
        <?php echo $form->error($this->model, 'phone'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($this->model, 'CustAddr'); ?>
        <?php echo $form->textArea($this->model, 'CustAddr'); ?>
        <?php echo $form->error($this->model, 'CustAddr'); ?>
    </div>

    <div class="row inline">
        <?php echo $form->labelEx($this->model, 'paymentType'); ?>
        <?php echo $form->radioButtonList($this->model, 'paymentType', $this->model->getPaymentTypes(), array('class'=>'inline', 'labelOptions'=>array('class'=>'inline'))); ?>
        <?php echo $form->error($this->model, 'paymentType'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($this->model, 'OrderDetails'); ?>
        <?php echo $form->textArea($this->model, 'OrderDetails'); ?>
        <?php echo $form->error($this->model, 'OrderDetails'); ?>
    </div>

    <div class="row buttons">
    	<?php echo CHtml::image($this->owner->template . '/images/shop/yandex-money-icon.jpg', '', array('class'=>'dorder-payment-yandex-btn'))?>
        <?php echo CHtml::submitButton($this->submitTitle, array('class'=>'ym-form-submit-btn '.$this->submitCssClass)); ?>
    </div>

    <?php $this->endWidget(); ?>
</div>

<script>
$(function() {
	$('#dorder-yandex-form label[for^=\'DOrder_models_YandexForm_paymentType\']').css('cursor', 'pointer');
	// _dorder-yandex-form-paymentType-handsup-id -> _dorderYfpthID
	var _dorderYfpthID = $('#dorder-yandex-form :radio[id^=\'DOrder_models_YandexForm_paymentType\'][value=\'Handsup\']').attr('id');
	$('#dorder-yandex-form [id^=\'DOrder_models_YandexForm_paymentType\']').on('change', function(e) {
		if($('#'+_dorderYfpthID+':checked').size()) {
			$('.dorder-payment-yandex-btn').hide();
		}
		else {
			$('.dorder-payment-yandex-btn').show();
		}
	});
});
</script>