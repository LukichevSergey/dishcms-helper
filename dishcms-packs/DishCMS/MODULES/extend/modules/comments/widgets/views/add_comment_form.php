<?php
/** @var \extend\modules\comments\widgets\AddCommentForm $this */
/** @var \crud\models\ar\extend\modules\comments\Comment $model */
use common\components\helpers\HArray as A;
use extend\modules\comments\components\helpers\HComment;

$t=HComment::t();
?>
<?= \CHtml::openTag($this->tag, A::m(['class'=>'comments__form-add', 'data-js'=>'js-comments__form-add'], $this->tagOptions)); ?>
<div class="comments__form-add js-comments__form-add">
    <h2><?= $t('widgets.addCommentForm.title'); ?></h2>
    
    <?php $form=$this->beginWidget('\CActiveForm', A::m([
        'id'=>'comments__form-add',
        'enableClientValidation'=>true,
        'action'=>\Yii::app()->createUrl($this->action, ['cid'=>'extend_comments']),
        'clientOptions'=>[
            'validateOnSubmit'=>true,
            'validateOnChange'=>false
        ],
    ], $this->formOptions)); ?>
    <?= \CHtml::hiddenField('hash', $model->model_hash); ?>
	<?= $form->hiddenField($model, 'model_id'); ?>

	<?php if(empty($disableRating)) {
	    $this->widget('\common\widgets\form\RadioListField', A::m(compact('form', 'model'), [
	        'attribute'=>'rating',
	        'data'=>[1=>1, 2=>2, 3=>3, 4=>4, 5=>5]
	    ]));
	} ?>
	
	<?php $this->widget('\common\widgets\form\TextField', A::m(compact('form', 'model'), ['attribute'=>'name', 'htmlOptions'=>['maxlength'=>255]])); ?>
	<?php $this->widget('\common\widgets\form\TextAreaField', A::m(compact('form', 'model'), ['attribute'=>'comment', 'htmlOptions'=>[]])); ?>
	
    <div class="row buttons">
        <?= CHtml::ajaxSubmitButton(
            $t('widgets.addCommentForm.btn.add'), 
            [$this->action, 'cid'=>'extend_comments', 'action'=>'add'], [
                'dataType'=>'json', 
                'success'=>'js:function(r){var $f=$("#comments__form-add"),$y=$.fn.yiiactiveform;((typeof r.success != "undefined") ? $f.parent().addClass(r.success?"successed":"failed").html(r.data.message) : $y.getSettings($f).attributes.forEach(function(a){$y.updateInput(a,r,$f);}));}'
            ], 
            ['class' => 'btn']
        ); ?>
    </div>
    <?php $this->endWidget(); ?>
<?= \CHtml::closeTag($this->tag); ?>