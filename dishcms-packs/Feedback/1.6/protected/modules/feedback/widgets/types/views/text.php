<?php
/** @var \feedback\widgets\types\TextTypeWidget $this */
/** @var FeedbackFactory $factory */
/** @var string $name attribute name. */
?>
<div>
	<?php echo $form->labelEx($factory->getModelFactory()->getModel(), $name); ?>
	<?php echo $form->textArea($factory->getModelFactory()->getModel(), $name, array(
		'class'=>'h50',
		'placeholder'=>$factory->getOption("attributes.{$name}.placeholder", '')));  
	?>
	<div style="display: none;">
	<?php echo $form->error($factory->getModelFactory()->getModel(), $name); ?>
	</div>
</div>