<?php
/** @var FeedbackWidget $this */
/** @var FeedbackFactory $factory */
?>
<div id="<?php echo $this->id; ?>" class="<?php echo $this->getOption('html', 'class'); ?>">
		<?php $form = $this->beginWidget('CActiveForm', array(
	        'id' =>  $this->getFormId(),
			'action' => $this->getFormAction(),			
	        'enableClientValidation' => true,
        	'enableAjaxValidation' => true,				
			'clientOptions' => array(
	            'validateOnSubmit' => true,
	            'validateOnChange' => false,
				'afterValidate' => 'js:feedback' . $this->getHash() . '.afterValidate',	
	        ),
			// 'htmlOptions'=>array('class'=>'form')
	    )); ?>
	    <?php echo CHtml::hiddenField('formId', $this->getFormId()); ?>
	    
	    <?php if($this->title): ?>
			<div class="cbHead">
				<span class="iconPhone"></span>
				<p><?php echo $this->title; ?></p>
			</div>
		<?php endif; ?>
	
		<div class="feedback-body">		
			
			<?php // @dump var_dump($factory->getModelFactory()->getAttributes()); ?>
			<?php foreach($factory->getModelFactory()->getAttributes() as $name=>$typeFactory): ?>
				<?php if($title = $factory->getOption("attributes.{$name}.title")): ?>
					<p><?php echo $title; ?></p>
				<?php endif; ?>
    			<?php $typeFactory->getModel()->widget($factory, $form, $this->params); ?>
			<?php endforeach; ?>
			
			<?php 
			// Captcha
			if($factory->getModelFactory()->getModel()->useCaptcha) {
				$this->widget('feedback.widgets.captcha.CaptchaWidget');
			}
			?>
			
			<?php echo CHtml::submitButton($factory->getOption('controls.send.title', 'Отправить'), array('class'=>'feedback-submit-button')); ?>
		</div>
			
		<div class="feedback-footer">
		</div>
		   
	     <?php $this->endWidget(); ?>
</div>
</center>

<script type="text/javascript">
// initialization
// clone FeedbackWidget object for use many feedback forms
var feedback<?php echo $this->getHash(); ?> = FeedbackWidget.clone(FeedbackWidget);
feedback<?php echo $this->getHash(); ?>.init("<?php echo $this->id; ?>");
</script>
