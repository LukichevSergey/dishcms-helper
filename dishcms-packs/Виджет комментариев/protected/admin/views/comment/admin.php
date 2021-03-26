<?php
/* @var $this CommentController */
/* @var $model Comment */

$this->breadcrumbs=array(
	'Комментарии'=>array('index'),
	'Управление',
);
?>
<h1>Редактирование</h1>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'comment-grid',
	'dataProvider'=>$model->search(),
	'itemsCssClass'=>'table table-striped  table-bordered table-hover items_sorter',
	'filter'=>$model,
	'columns'=>array(
		'id',
		'title',
		'description',
		'date',
        'moderated',
        'comment_model.model_name',
		'comment_model.model_id',
        array(            // display a column with "view", "update" and "delete" buttons
            'class'=>'CButtonColumn',
            'template'=>'{update}{delete}',
            'updateButtonImageUrl'=>false,
            'deleteButtonImageUrl'=>false,
            'buttons'=>array
            (
                'delete' => array
                (   
                    'label'=>'<span class="glyphicon glyphicon-remove"></span> ',
                    'options'=>array('title'=>'Удалить'),
                ),
                'update' => array
                (      
                    'label'=>'<span class="glyphicon glyphicon-pencil"></span> ',
                    'options'=>array('title'=>'Редактировать'),
                ),
            ),
        ),
	),
)); ?>
