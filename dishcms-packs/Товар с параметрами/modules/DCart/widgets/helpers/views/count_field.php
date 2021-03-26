<?php
/** \DCart\widgets\helpers\CountFieldWidget $this */
?>
<div class="dcart-widgets-helpers-count-field">
	<label for="<?php echo $this->id; ?>"><?php echo $this->label; ?>:</label>
    <?php echo CHtml::textField('count', 1, array(
    	'id' => $this->id, 
    	'class' => 'dcart-widgets-helpers-count-field-value',
    	'data-prompt-alert' => 'Неверное значение',
    	'maxlength' => 7
    )); ?>
</div>
