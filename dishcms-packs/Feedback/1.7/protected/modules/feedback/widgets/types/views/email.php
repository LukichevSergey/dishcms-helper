<?php
/** @var \feedback\widgets\types\EmailTypeWidget $this */
/** @var FeedbackFactory $factory */
/** @var string $name attribute name. */
?>

	<?php //echo $form->labelEx($factory->getModelFactory()->getModel(), $name); ?>
	<?php echo $form->error($factory->getModelFactory()->getModel(), $name); ?>
	<?php echo $form->textField($factory->getModelFactory()->getModel(), $name, array(
		'class' => 'inpt',
		'placeholder'=>$factory->getOption("attributes.{$name}.placeholder", ''))); 
	?>
