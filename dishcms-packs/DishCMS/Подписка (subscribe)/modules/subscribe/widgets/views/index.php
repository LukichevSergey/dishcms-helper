<?php 
$form = $this->beginWidget('CActiveForm', array(
			'action'=>Yii::app()->createAbsoluteUrl('/subscribe'),
		    'id'=>'user-form',
		    'enableAjaxValidation'=>true,
		    'enableClientValidation'=>false,
		    'clientOptions'=>array(
		         'validateOnSubmit'=>true,
		         'validateOnChange' => false,
		         'afterValidate' => 'js:function(form, data, hasError) { clientVal(form, data, hasError); }',

		     ),
		    'htmlOptions' => array(

		            //'enableClientValidation'=>false,
		            'validateOnChange' => false,

		    ),
	)); ?>
	<script type="text/javascript">
		function clientVal(form, data, hasError){
			$('#user-form input').removeClass('inpt-error')
			if(hasError) {
			    for(var i in data) $("#"+i).addClass("inpt-error");
			}
			else{
				$('#user-form').html(data.message);
			}
		}
	</script>
	<?php // echo $form->errorSummary($subscribes); ?>
<div class="input-group">
	<?php echo $form->textField($subscribes,'email', ['class'=>'form-control', 'placeholder'=>'Ваш email']); ?>
	<div style="display:none"><?php echo $form->error($subscribes, 'email'); ?></div>
	<span class="input-group-btn">
		<?php echo CHtml::submitButton('Подписаться', ['class'=>'btn btn-default']); ?>
	</span>
</div>
<?php $this->endWidget(); ?>

