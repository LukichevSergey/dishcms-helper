<li value="<?php echo $widget->dataProvider->pagination->pageSize*$widget->dataProvider->pagination->currentPage + $index + 1;?>">
	<?=CHtml::link($data->title, array('shop/category', 'id'=>$data->id)); ?>
	<p><?=preg_replace('/{[^}]+\}/','',$data->getIntro())?></p>
</li>