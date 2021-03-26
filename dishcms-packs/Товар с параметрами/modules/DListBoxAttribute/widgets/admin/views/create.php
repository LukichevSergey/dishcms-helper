<?php
/** @var \DListBoxAttribute\widgets\admin\CreateWidget $this */
/** @var \DListBoxAttribute\models\DListBoxAttribute $model */
?>
<h1><?php echo $this->getTitle(); ?></h1>
<?php $this->render('_form', compact('model')); ?>

