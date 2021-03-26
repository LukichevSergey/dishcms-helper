<?php
use common\components\helpers\HArray as A;
?>
<?php $this->widget('\common\widgets\form\CheckboxField', A::m(compact('form', 'model'), ['attribute'=>'disabled'])); ?>


<? $this->widget('\common\ext\file\widgets\UploadFile', [
	'behavior'=>$model->imageBehavior, 
	'form'=>$form, 
	'actionDelete'=>$this->createAction('removeImage'),
    'tmbWidth'=>970,
    'tmbHeight'=>300,
    'view'=>'panel_upload_image'
]); ?>

<div class="alert alert-info">При загрузке новой карты SVG необходимо будет заново привязывать все этажи фасада</div>

<? $this->widget('\common\ext\file\widgets\UploadFile', [
	'behavior'=>$model->svgBehavior, 
	'form'=>$form, 
	'actionDelete'=>$this->createAction('removeFile'),
    'view'=>'panel_upload_file'
]); ?>