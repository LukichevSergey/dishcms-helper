<?php
/** @var \feedback\widgets\types\StringTypeWidget $this */
/** @var FeedbackFactory $factory */
/** @var string $name attribute name. */
?>

	<?php //echo $form->labelEx($factory->getModelFactory()->getModel(), $name); ?>
	<div style="display: none;">
		<?php echo $form->error($factory->getModelFactory()->getModel(), $name); ?>
	</div>
	<?php echo $form->textField($factory->getModelFactory()->getModel(), $name, array(
		'class'=>'inpt',
		'placeholder'=>$factory->getOption("attributes.{$name}.placeholder", ''))); 
	?>
