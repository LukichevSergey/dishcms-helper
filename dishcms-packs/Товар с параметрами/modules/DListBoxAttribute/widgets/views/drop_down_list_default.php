<?php
/** @var \DListBoxAttribute\widgets\DropDownListWidget $this */
/** @var array $items */
?>
<?php echo \CHtml::activeLabelEx($this->model, $this->attributeOwner); ?>
<?php echo \CHtml::activeDropDownList($this->model, $this->attributeOwner, $items, array(
	'prompt' => $this->prompt,
	'data-prompt-alert' => $this->promptAlert, 
	'options' => $this->getOptions(), 
	'class' => $this->cssClass,
)); ?>