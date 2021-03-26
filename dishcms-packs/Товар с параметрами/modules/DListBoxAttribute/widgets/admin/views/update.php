<?php
/** @var \DListBoxAttribute\widgets\admin\UpdateWidget $this */
?>
<h1><?php echo $this->getTitle(); ?></h1>
<?php $this->render('_form', compact('model')); ?>