<?php
/** @var \feedback\widgets\types\StringTypeWidget $this */
/** @var FeedbackFactory $factory */
/** @var string $name attribute name. */
?>
<div>
	<?php echo $form->labelEx($factory->getModelFactory()->getModel(), $name); ?>
	<?php echo $form->textField($factory->getModelFactory()->getModel(), $name, array(
		'class'=>'w30', 
		'maxlength'=>15, 
		'placeholder'=>$factory->getOption("attributes.{$name}.placeholder", ''))); 
	?>
	<?php echo $form->error($factory->getModelFactory()->getModel(), $name); ?>
</div>