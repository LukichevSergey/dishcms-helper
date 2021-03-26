<?php
/** @var \feedback\widgets\types\PhoneTypeWidget $this */
/** @var FeedbackFactory $factory */
/** @var string $name attribute name. */
$hash = rand(0, 1000000);
?>

	<?php // echo $form->labelEx($factory->getModelFactory()->getModel(), $name); ?>
    <div style="display: none;">
		<?php echo $form->error($factory->getModelFactory()->getModel(), $name); ?>
	</div>
	<?php
        $this->widget('CMaskedTextField', array(
            'model' => $factory->getModelFactory()->getModel(),
            'attribute' => $name,
            'mask' => '+7 ( 999 ) 999 - 99 - 99',
			'htmlOptions' => array(
				'class'=>'inpt ' . $name . $hash, 
				'placeholder'=>$factory->getOption("attributes.{$name}.placeholder", '+7 ( ___ ) ___ - __ - __'))
        ));
    ?>

<script type="text/javascript">
/*<![CDATA[*/
jQuery(function($) {
	jQuery(".<?php echo $name . $hash;?>").mask("+7 ( 999 ) 999 - 99 - 99");
});
/*]]>*/
</script>