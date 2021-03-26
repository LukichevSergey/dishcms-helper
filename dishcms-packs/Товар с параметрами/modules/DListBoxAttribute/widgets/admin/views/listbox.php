<?php
/** @var \DListBoxAttribute\widgets\admin\ListBoxWidget $this */
/** @var \CActiveForm $form */ 
/** @var \DListBoxAttribute $model */ 
?>

<?php echo $form->labelEx($model, $this->attributeOwner); ?>
<?php echo $form->error($model, $this->attributeOwner); ?>
<?php echo $form->listBox($model, $this->attributeOwner, $this->getItems(), array(
	'multiple' => true, 
	'options' => $this->getHtmlOptions(), 
	'size' => 7, 
	'class' => $this->cssClass
)); ?>        
