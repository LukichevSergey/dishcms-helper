<?php
/** @var \CActiveForm $form */
/** @var \extend\modules\points\models\PointSettings $model */
use common\components\helpers\HArray as A;

$this->widget('\common\widgets\form\TextField', A::m(compact('form', 'model'), ['attribute'=>'apikey']));

$this->widget('\common\ext\file\widgets\UploadFile', [
    'behavior'=>$model->placemarkIconBehavior,
    'form'=>$form,
    'actionDelete'=>$this->createAction('removeImage'),
    'tmbWidth'=>200,
    'tmbHeight'=>200,
    'view'=>'panel_upload_image'
]);

$this->widget('\ext\uploader\widgets\ClearButton', [
    'label'=>'Удалить временные файлы вкладки "Фотографии"',
    'clearUrl'=>'/extend/points/admin/crud/clearFiles',
    'htmlOptions'=>['class'=>'btn btn-warning']
]);