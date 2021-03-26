<?php
/** @var \feedback\widgets\types\TextTypeWidget $this */
/** @var FeedbackFactory $factory */
/** @var string $name attribute name. */
?>

<?php echo $form->hiddenField($factory->getModelFactory()->getModel(), $name); ?>
