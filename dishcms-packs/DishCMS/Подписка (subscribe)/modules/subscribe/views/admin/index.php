<?php if(Yii::app()->user->hasFlash('deleted')):?>
<div class="alert alert-success all_right">Успешно удалено!</div>
<?php endif; ?>
<?php if(Yii::app()->user->hasFlash('not_deleted')):?>
<div class="alert alert-danger">В ходе удаления произошли ошибки!</div>
<?php endif; ?>

<?php echo CHtml::link('Список сообщений', $this->createAbsoluteUrl('subscribe/list')); ?>

<div class="classic">
<?php
$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'my-model-grid',
    'dataProvider' => $dataProvider,

    'columns' => array(
        'id::ID',
        'email',
        'active',
        'link'=>array(

                'header'=>'',
                'type'=>'raw',
                'value'=> 'CHtml::button( $data->active ? "Деактивировать" : "Сделать активным" ,array("onclick"=>"document.location.href=\'".Yii::app()->controller->createUrl("admin/active",array("id"=>$data->id))."\'"))',
        ),      
            array(
                'class'=>'CButtonColumn',
                'template'=>'{delete}',
                'buttons'=>array(

                'delete' => array(
                        'url'=>'$this->grid->controller->createUrl("/admin/subscribe/delete", array("id"=>$data->id, "class"=>"s"))',
                    ),
                ),
            ),

    ),
));
?>
</div>




<style>
    #my-model-grid_clink{
        width: 1px;
    }
    .items  input{
        width: 10px;
        width: 165px;
    }
</style>