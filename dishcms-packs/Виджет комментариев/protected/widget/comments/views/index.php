<?php if ($comments = $relation_model->comment_list): ?>
	<div class="comment-list-wrapper">
		<h3>Комментарии</h3>
		<div class="comment-list">
			<?php foreach ($comments as $comment): ?>
				<div class="comment-list__item">
					<div class="comment-list__item-date"><?= $comment->comment_info->date ?></div>
					<div class="comment-list__item-title"><?= $comment->comment_info->title ?></div>
					<div class="comment-list__item-description"><?= $comment->comment_info->description ?></div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
<?php endif; ?>
<div class="comment-form form">
	<h3>Оставить комментарий</h3>
	<?php
	/* @var $this CommentController */
	/* @var $model Comment */
	/* @var $form CActiveForm */
	?>
	
	<?php if ($message): ?>
		<div class="form-message"><?= $message ?></div>
	<?php endif; ?>

	<div class="form form_full-width">

	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'comment-form',
		'enableAjaxValidation' => false,
		'enableClientValidation'=>true,
		'clientOptions'=>array(
		    'validateOnSubmit'=>true,
		    'validateOnChange'=>false
		),
	)); ?>

		<?php echo $form->errorSummary($model); ?>

		<div class="row">
			<?php echo $form->labelEx($model,'title'); ?>
			<?php echo $form->textField($model,'title',array('class'=>'form-control')); ?>
			<?php echo $form->error($model,'title'); ?>
		</div>

		<div class="row">
			<?php echo $form->labelEx($model,'description'); ?>
			<?php echo $form->textArea($model,'description',array('class'=>'form-control')); ?>
			<?php echo $form->error($model,'description'); ?>
		</div>

		<div class="row buttons">
			<div class="left">
				<?php echo CHtml::submitButton('Отправить', array('class'=>'btn btn-primary')); ?>
			</div>
		</div>

	<?php $this->endWidget(); ?>

	</div><!-- form -->
</div>