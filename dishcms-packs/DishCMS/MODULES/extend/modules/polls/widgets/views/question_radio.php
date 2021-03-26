<?php
/** @var \extend\modules\polls\widgets\Question $this */

$isSmallSize=true;
foreach($this->getAnswers() as $answer) {
    $isSmallSize = $isSmallSize && (mb_strlen($answer->label) < 15);
}
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
    		<?= \CHtml::label($answer->label, $answer->fieldId, [
    		    'class'=>'quiz__label label label_tp_radio text' . ($isSmallSize ? ' quiz__label_sz_sm' : '')
    		]); ?>
    	<?php endforeach; ?>
   	</div>
</fieldset>