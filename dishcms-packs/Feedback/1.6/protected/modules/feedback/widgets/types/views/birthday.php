<?php
/** @var \feedback\widgets\types\StringTypeWidget $this */
/** @var FeedbackFactory $factory */
/** @var string $name attribute name. */

// @var \feedback\models\FeedbackModel
$model = $factory->getModelFactory()->getModel();
?>
<div>
	<?php echo $form->labelEx($model, $name); ?>
	<?php $this->widget('widgets.MaskedJuiDatePicker.MaskedJuiDatePicker',array(
			'language'=>'ru',
      		'name'=>preg_replace('/\\\\+/', '_', get_class($model)) . "[{$name}]",
		     //the new mask parmether
      		'mask'=>'99.99.9999',
			// additional javascript options for the date picker plugin
      		'options'=>array(
          		'showAnim'=>'fold',
      		),
			'value'=>Yii::app()->dateFormatter->formatDateTime($model->isNewRecord ? time() : $model->created),
      		'htmlOptions'=>array(
          		'style'=>'height:20px;',
				'placeholder'=>$factory->getOption("attributes.{$name}.placeholder", '__.__.____')
      		),
  		));
	?>
	<?php echo $form->error($factory->getModelFactory()->getModel(), $name); ?>
</div>