<li value="<?php echo $widget->dataProvider->pagination->pageSize*$widget->dataProvider->pagination->currentPage + $index + 1;?>">
	<?php echo CHtml::link($data->title, array('site/event', 'id'=>$data->id)); ?>
</li>