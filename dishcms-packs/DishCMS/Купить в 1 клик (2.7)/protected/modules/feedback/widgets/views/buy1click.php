<?php
/** @var FeedbackWidget $this */
/** @var FeedbackFactory $factory */
use common\components\helpers\HYii as Y;

Y::js('feedback'.$this->getHash(),
    'var feedback'.$this->getHash().'=FeedbackWidget.clone(FeedbackWidget);feedback'.$this->getHash().'.init("'.$this->id.'");'
);
$params = [];
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
        <? if(is_callable($this->onBefore)) call_user_func($this->onBefore, $factory->getModelFactory()->getModel()); ?>

        <?php if($this->title): ?>
            <div class="cbHead">
                <span class="iconPhone"></span>
                <p><?php echo $this->title; ?></p>
            </div>
        <?php endif; ?>

        <div class="cbSubHead"><?= $this->params['product_title']; ?></div>
    
        <div class="feedback-body">     

            <?php foreach($factory->getModelFactory()->getAttributes() as $name=>$typeFactory): ?>
                <? if(in_array($name, $this->skip)) continue; ?>
                <?php if($title = $factory->getOption("attributes.{$name}.title")): ?>
                    <p><?php echo $title; ?></p>
                <?php endif; ?>
                <? 
                if($name == 'product_id') $params=['htmlOptions'=>['value'=>$this->params['product_id']]];
                else $params = []; 
                ?>
                <?php $typeFactory->getModel()->widget($factory, $form, $params); ?>
            <?php endforeach; ?>

            <?php 
            // Captcha
            if($factory->getModelFactory()->getModel()->useCaptcha) {
                $this->widget('feedback.widgets.captcha.CaptchaWidget');
            }
            ?>

            <?php echo CHtml::submitButton($factory->getOption('controls.send.title', 'Отправить'), array('class'=>'feedback-submit-button btn')); ?>
        </div>

        <div class="feedback-footer">
        </div>

         <?php $this->endWidget(); ?>
</div>

