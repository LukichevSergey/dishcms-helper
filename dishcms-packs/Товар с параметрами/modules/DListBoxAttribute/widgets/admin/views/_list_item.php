<tr id="d-list-box-attribute-<?php echo $data->id; ?>" class="row<?php echo $index % 2 ? 0 : 1; ?>">
	<td class="title">
		<?php echo \CHtml::link($data->title, array("dListBoxAttribute/{$attribute}/update/{$data->id}")); ?>
	</td>
	<td><?php echo \CHtml::ajaxLink('удалить', $this->createUrl("dListBoxAttribute/{$attribute}/delete/{$data->id}"),
		array(
            'type'=>'post',
			'dataType'=>'json',
            'data'=>array('ajax'=>1),
			'beforeSend' => 'DListBoxAttributeAdminWidget.removeBeforeSend',
        	'success' => 'DListBoxAttributeAdminWidget.removeSuccess'
    	)); ?>
	</td>
</tr>
