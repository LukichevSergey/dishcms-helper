<script type="text/javascript">
    $(function(){
        var aliasName = '<?php echo CHtml::activeId($model, 'alias') ?>',
            $aliasInput = $('#' + aliasName),
            $titleInput = $('#<?php echo CHtml::activeId($model, 'title') ?>');
            $titleInput.keyup(function(){
                $titleInput.translit('send', '#' + aliasName);
                $aliasInput.val($aliasInput.val().toLowerCase());
            });
    });
</script>
<div class="row">
    <?php echo $form->labelEx($model,'message'); ?>
    <?php $this->widget('admin.widget.CmsEditor.CmsEditor', array(
        'model'=>$model,
        'attribute'=>'message',
        'htmlOptions'=>array('class'=>'big')
    )); ?>
    <?php echo $form->error($model,'message'); ?>
</div>

