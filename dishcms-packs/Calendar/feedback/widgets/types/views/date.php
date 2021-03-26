<?php
/** @var \feedback\widgets\types\StringTypeWidget $this */
/** @var FeedbackFactory $factory */
/** @var string $name attribute name. */

// @var \feedback\models\FeedbackModel
$model = $factory->getModelFactory()->getModel();
// @var int  
$hash = rand(0, 1000000);
$nameHashed = $name . $hash;
?>
<div style="position:relative">
	<?php // echo $form->labelEx($model, $name); ?>
	<?php $this->widget('CMaskedTextField', array(
            'model' => $model,
            'attribute' => $name,
            'mask' => '99 . 99 . 9999',
            'placeholder' => $factory->getOption("attributes.{$name}.placeholder", '__ . __ . ____'),
            'value' => Yii::app()->dateFormatter->formatDateTime($model->isNewRecord ? null : $model->created),
            'htmlOptions'=>array(
				'id'=> $nameHashed .'-id',
            	'class'=>'inpt ' . $nameHashed,
				'placeholder' => $factory->getOption("attributes.{$name}.placeholder", '__ . __ . ____'),
            ),
        ));
	?>
	<?php /* $this->widget('widgets.MaskedJuiDatePicker.MaskedJuiDatePicker',array(
			'language'=>'ru',
      		'name'=>preg_replace('/\\\\+/', '_', get_class($model)) . "[{$name}]",
		     //the new mask parmether
      		'mask'=>'99 . 99 . 9999',
			// additional javascript options for the date picker plugin
      		'options'=>array(
          		'showAnim'=>'fold',
      		),
			'value'=>Yii::app()->dateFormatter->formatDateTime($model->isNewRecord ? null : $model->created),
      		'htmlOptions'=>array(
				'class'=>'inpt ' . $nameHashed,
				'placeholder'=>$factory->getOption("attributes.{$name}.placeholder", '__ . __ . ____')
      		),
  		)); */
	?>
	<div style="display: none;">
		<?php echo $form->error($model, $name); ?>
	</div>
	<div class="input-select-btn btn-<?php echo $nameHashed; ?>"></div>
</div>

<?php \Yii::app()->clientScript->registerScript($nameHashed, 
'jQuery(".'.$nameHashed.'").mask("99 . 99 . 9999");
jQuery(".'.$nameHashed.'").datepicker(jQuery.extend({showMonthAfterYear:false}, jQuery.datepicker.regional["ru"], {"showAnim":"fold"}));
jQuery(".btn-'.$nameHashed.'").on("click", function() { jQuery(".'.$nameHashed.'").focus(); });'
); ?>