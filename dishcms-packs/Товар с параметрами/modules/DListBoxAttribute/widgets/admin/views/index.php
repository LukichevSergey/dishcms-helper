<?php
/** @var \DListBoxAttribute\widgets\admin\ListWidget $this */
/** @var \CActiveDataProvider[DListBoxAttribute] $dataProvider */ 
?>
<h1><?php echo $this->getTitle(); ?></h1>

<?php \HtmlHelper::flash('success'); ?>

<div style="margin-bottom: 15px;">
	<?php echo \CHtml::link('Добавить', array("dListBoxAttribute/{$this->attribute}/create"), array('class'=>'default-button')); ?>
</div>

<?php if (!$dataProvider->getItemCount()): ?>
	<p>Значений нет</p>
<?php  else: ?>
	<table class="adminList">
	    <thead>
	    <tr>
	        <th>Название</th>
	        <th style="width: 1%"></th>
	    </tr>
	    </thead>
    	<?php $this->owner->widget('zii.widgets.CListView', array(
			'dataProvider' => $dataProvider,
			'itemView' => 'DListBoxAttribute.widgets.admin.views._list_item',
			'itemsTagName' => 'tbody',
    		'viewData' => array('attribute' => $this->attribute)
		)); ?>
	</table>	
<?php endif; ?>

<div style="margin-top: 15px;">
	<?php echo \CHtml::link('Добавить', array("dListBoxAttribute/{$this->attribute}/create"), array('class'=>'default-button')); ?>
</div>
