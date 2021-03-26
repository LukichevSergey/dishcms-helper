<?php
/** @var \extend\modules\polls\widgets\Poll $this */
use common\components\helpers\HYii as Y;
$formId=uniqid('quiz');
Y::js(false, 'window.extendModulesPollsWidgetsPoll.init('.\CJavaScript::jsonEncode(['form'=>$formId]).')', \CClientScript::POS_READY);
?>
<div class="quiz__col">
	<h1><?= $this->getPoll()->title; ?></h1>
	<p class="quiz__text text"><?= $this->getPoll()->text; ?></p>
	<form id="<?=$formId?>" class="quiz__form"><?php 
        foreach($this->getQuestions() as $question) {
            $this->owner->widget('\extend\modules\polls\widgets\Question', compact('question'));
        }
        ?>
        <?= \CHtml::ajaxSubmitButton('Отправить', ['/common/crud/default/ajax', 'action'=>'add', 'cid'=>'extend_polls'], [
            'type'=>'post',
            'dataType'=>'json',
            'beforeSend'=>'js:window.extendModulesPollsWidgetsPoll.onAjaxBeforeSend',
            'success'=>'js:function(r){console.log(r);if(r.success){window.location.reload();}}'
        ], ['class'=>'quiz__submit btn btn_wd_md'])?>
	</form>
</div>