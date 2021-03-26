<?php
/** @var \extend\modules\polls\widgets\Question $this */
?>
<fieldset class="quiz__fieldset">
	<legend class="quiz__legend text text_fw_bold"><?= $this->getQuestionTitle(); ?></legend>
	<div class="quiz__fieldset-row">
	<?php foreach($this->getAnswers() as $answer): ?>
		<?= \CHtml::radioButton($answer->fieldName, false, [
		    'id'=>$answer->fieldId,
		    'value'=>$answer->hash,
		    'class'=>'quiz__radio visually-hidden' . ($this->isRequired() ? ' required' : '')
		]); ?>
		<?= \CHtml::label($answer->label, $answer->fieldId, ['class'=>'quiz__label-btn label-btn text']); ?>
	<?php endforeach; ?>
	</div>
</fieldset>