<?php
/** @var \feedback\widgets\types\ListTypeWidget $this */
/** @var FeedbackFactory $factory */
/** @var string $name attribute name. */
?>
	<?php //echo $form->labelEx($factory->getModelFactory()->getModel(), $name); ?>
	<div style="display: none;">
		<?php echo $form->error($factory->getModelFactory()->getModel(), $name); ?>
	</div>
	<?php $options = $this->params['selected'] 
				? array($this->params['selected'] => array(
						'selected'=>true, 
						'label'=>$this->items[$this->params['selected']]))
				: array(); ?>
	<?php echo $form->dropDownList(
		$factory->getModelFactory()->getModel(), 
		$name, 
		$this->items,
		array(
			'class'=>'inpt',
			'placeholder'=>$factory->getOption("attributes.{$name}.placeholder", ''),
			'options' => $options
		)); 
	?>
