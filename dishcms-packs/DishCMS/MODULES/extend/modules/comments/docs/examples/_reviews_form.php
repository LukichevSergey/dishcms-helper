<?php
/** @var \extend\modules\comments\widgets\AddCommentForm $this */
/** @var \crud\models\ar\extend\modules\comments\Comment $model */
use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use extend\modules\comments\components\helpers\HComment;

$t=HComment::t();

$baseUrl=\Yii::app()->baseUrl;
Y::jsFile('/js/jquery.rating.pack.js');
Y::css('reviews_form', 
"div.star-rating{float:left;width:18px;height:16px;text-indent:-999em;cursor:pointer;display:block;background:transparent;overflow:hidden;}
div.star-rating {background: url({$baseUrl}/images/marks/star.png) 0 16px; height:16px;}
div.star-rating-hover {background: url({$baseUrl}/images/marks/star.png); height:16px;}
div.star-rating-on {background: url({$baseUrl}/images/marks/star.png); height:16px;}
span.star-view {background: url({$baseUrl}/images/marks/star.png); height:16px; display: inline-block;vertical-align: top; float: right;}
.star-1 {width:18px;}
.star-2 {width:36px;}
.star-3 {width:54px;}
.star-4 {width:72px;}
.star-5 {width:90px;}
");
?>
<div style="display: none;">
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

	<?php if(empty($disableRating)): ?>
	    <span style="display: inline-block;" id="ProductReview_mark">
	    <?php $this->widget('\common\widgets\form\RadioListField', A::m(compact('form', 'model'), [
	        'attribute'=>'rating',
	        'data'=>[1=>1, 2=>2, 3=>3, 4=>4, 5=>5],
	        'labelOptions'=>['style' => 'display:inline-block; width:100px'],
	        'htmlOptions'=>['template'=>'{input}', 'separator'=>'', 'class'=>'star', 'container'=>'div']
	    ])); ?>
	    </span>
	<?php endif; ?>
	
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
</div>