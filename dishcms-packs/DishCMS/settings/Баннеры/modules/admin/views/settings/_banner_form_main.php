<?php
/** @var \settings\modules\admin\controllers\DefaultController $this */
/** @var \BannerSettings $model */
/** @var \CActiveForm $form */
use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;

$tbtn=Y::ct('CommonModule.btn', 'common');
	
$this->widget('\common\widgets\form\CheckboxField', A::m(compact('form', 'model'), ['attribute'=>'main_active'])); 

$this->widget('\common\widgets\form\TextField', A::m(compact('form', 'model'), ['attribute'=>'main_url']));
$this->widget('\common\widgets\form\TextField', A::m(compact('form', 'model'), ['attribute'=>'main_url_label']));
	
$this->widget('\common\ext\file\widgets\UploadFile', [
	'behavior'=>$model->mainImageBehavior, 
	'form'=>$form, 
    'tmbWidth'=>750,
    'tmbHeight'=>170,
    'view'=>'panel_upload_image'
]);	

$this->widget('\common\widgets\form\TinyMceField', A::m(compact('form', 'model'), ['attribute'=>'main_text']));
?>