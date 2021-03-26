<li value="<?php echo $widget->dataProvider->pagination->pageSize*$widget->dataProvider->pagination->currentPage + $index + 1;?>">
	<?php echo CHtml::link($data->title, array('site/page', 'id'=>$data->id)); ?>
	<p><?php echo $data->getIntro(); ?></p>
</li>