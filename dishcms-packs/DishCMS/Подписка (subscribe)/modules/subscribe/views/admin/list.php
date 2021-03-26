<style>.subscribe__btn-send img{width: 32px;}</style>
<div class="classic">
<a class="btn btn-link" href="<?=$this->createUrl('subscribe/index');?>">Подписчики</a>
<a class="btn btn-primary" href="<?=$this->createUrl('subscribe/edit');?>">Создать новое сообщение</a>
<?php
#foreach ($dataProvider->data as $key => $q) {
 #   
#
 #   $dataProvider->data[$key]->message = strip_tags( $dataProvider->data[$key]->message );
#}


#id message date send_time from from_name


#echo CHtml::image(Yii::app()->controller->module->registerImage('send-email.png'), "send-email");


$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'my-model-grid',
    'dataProvider' => $dataProvider,
    'columns' => array(
        'id::ID',
        'theme',
        'send_time',
        'from',
        'from_name',

        array(
            'class'=>'CButtonColumn',
            'template'=>'{send}',
            #'template'=>'{delete}',
            'buttons'=>array(
            'send' => array(
            	'label'=>'Отправить',
            	'options'=>['class'=>'subscribe__btn-send'],
                'class'=>'send',
                'imageUrl'=>Yii::app()->getModule('subscribe')->registerImage('send-email.png'),
                'url'=>'$this->grid->controller->createUrl("/subscribe/admin/send", array("id"=>$data->id))',
                ),
            ),
        ),

        array(
            'class'=>'CButtonColumn',
            'template'=>'{update}{delete}',
            #'template'=>'{delete}',
            'buttons'=>array(
            'send' => array(
                      'label'=>'Add',
                      // 'imageUrl'=>Yii::app()->controller->module->registerImage('send-email.png'),
                      'url'=>'$this->grid->controller->createUrl("/Extras/create", array("bid"=>"$data->id", "asDialog"=>1,"gridId"=>$this->grid->id))',
                      'click'=>'function(){$("#cru-frame").attr("src",$(this).attr("href")); $("#cru-dialog").dialog("open");  return false;}',
                      #'visible'=>'($data->id===null)?true:false;'
                      ),
            'delete' => array(
                'id'=>'id',
                'click'=>'function(){ }',
                ),
            ),
        ),

    ),
));
?>
</div>