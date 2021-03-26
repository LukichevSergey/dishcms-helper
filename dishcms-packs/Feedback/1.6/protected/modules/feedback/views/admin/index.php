<?php 
/** @var \feedback\controllers\AdminController $this */
/** @var \feedback\components\FeedbackFactory $factory */
/** @var [\feedback\models\FeedbackModel]\CActiveDataProvider $dataProvider */
/** @var int $uncompletedCount */ 
/** @var string $title */

?>
<div id="feedback-<?php echo $factory->getId(); ?>">
<h1>
	<?php echo $title ?>
	<span class="notify feedback-uncompleted-count feedback-<?php echo $factory->getId(); ?>-count-in-title"><?php echo $uncompletedCount; ?></span>
</h1>

<?php if (!$dataProvider->getItemCount()): ?>
<p><?php echo $factory->getOption('emptyMessage', 'Заявок нет'); ?></p>
<?php else: ?>
<table id="feedback-table" class="adminList">
    <thead>
    <tr>
        <th style="width:1%"></th>
    	<th>&nbsp;</th>    	
        <th style="width:65px;text-align:center;">Дата</th>
    	<th style="width:1%" title="Обработано">Обраб.</th>
    	<th style="width:1%"></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach($dataProvider->getData() as $feedback): ?>
    <tr id="feedback-row-<?php echo $feedback->id; ?>" class="row<?php echo $feedback->id % 2 ? 0 : 1; ?>" valign="top">
    	<td><?php echo $feedback->id; ?>.</td>
        <td class="title">
        	<?php $model = $factory->getModelFactory()->getModel(); ?>
        	<?php foreach($factory->getModelFactory()->getAttributes() as $name=>$typeFactory):?>
        		<b><?php echo $model->getAttributeLabel($name); ?>:</b> <?php echo $typeFactory->getModel()->format($feedback->$name); ?><br />
        	<?php endforeach; ?>       		
        </td>        
    	<td align="center"><?php echo str_replace(' ', '<br />', $feedback->created); ?></td>
    	<td align="center"><div class="mark <?php echo !$feedback->completed ? 'marked' : 'unmarked'; ?>" data-item="<?php echo $feedback->id; ?>"></div></td>
        <td><?php echo CHtml::link('удалить', 'javascript:;', array('class'=>'feedback-btn-remove', 'data-item'=>$feedback->id)); ?></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<br />
<?php $this->ownerController->widget('\CLinkPager', array(
    'pages' => $dataProvider->getPagination(),	
)); ?>

<script type="text/javascript">
$(function() {
	FeedbackAdmin.init("<?php echo $factory->getId(); ?>");
});
</script>
<?php endif; ?>

</div>