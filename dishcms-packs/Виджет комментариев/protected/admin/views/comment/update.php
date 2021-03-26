<?php
/* @var $this CommentController */
/* @var $model Comment */

$this->breadcrumbs=array(
	'Комментарии'=>array('index'),
	$model->title . ' - Обновление',
);

?>

<h1>Обновление комментария <?php echo $model->id; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>