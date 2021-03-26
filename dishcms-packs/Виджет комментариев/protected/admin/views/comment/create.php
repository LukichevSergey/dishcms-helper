<?php
/* @var $this CommentController */
/* @var $model Comment */

$this->breadcrumbs=array(
	'Comments'=>array('index'),
	'Создание',
);
?>

<h1>Создание Comment</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>