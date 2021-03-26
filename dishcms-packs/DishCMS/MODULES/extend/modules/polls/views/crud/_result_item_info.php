<?php
/** @var \crud\models\ar\extend\modules\polls\models\Result $data */
use crud\models\ar\extend\modules\polls\models\Result;

$results=Result::model()->findAllByAttributes(['result_hash'=>$data->result_hash], ['order'=>'question_id']);
?>
<div style="margin-bottom:5px;font-weight:bold;border-bottom:1px solid #ccc;"><?= $data->poll->title; ?></div>
<small>
<?php $lastQuestionId=null; ?>
<?php foreach($results as $result): ?>
	<?php $question=$result->getRelated('question'); ?>
	<?php if($lastQuestionId <> $question->id): ?>
		<?php if($lastQuestionId): ?></ul></div><?php endif; $lastQuestionId=$question->id; ?>
		<div><b><?= $question->title; ?></b><ul style="list-style:circle;list-style-position:inside">
	<?php endif; ?>	
	<li style="margin:0;"><?= $question->answersBehavior->find('hash', $result->answer_hash, ['v'=>'title']); ?></li>
<?php endforeach; ?>
</ul></div>
</small>