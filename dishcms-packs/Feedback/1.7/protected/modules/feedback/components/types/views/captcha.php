<?php
/** @var \feedback\widgets\inputField\Captcha $this */
?>
<table>
	<tr>
		<td class="c-captcha">
			<?php $this->widget('widgets.captcha.CCaptchaFix', array('showRefreshButton'=>true, 'buttonLabel'=>'Сменить картинку')); ?>
		</td>
		<td class="verifyCode">
			<?php echo $form->labelEx($model,'verifyCode'); ?>
			<?php echo $form->textField($model, 'verifyCode'); ?>
			<?php echo $form->error($model,'verifyCode'); ?>
		</td>
	</tr>
</table>
