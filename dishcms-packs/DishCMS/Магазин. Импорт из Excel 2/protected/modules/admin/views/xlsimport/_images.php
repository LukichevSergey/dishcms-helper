<?php
use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HFile;

$imagesPath=Yii::getPathOfAlias('webroot.uploads.xlsimport');
$imagesUrl='/uploads/xlsimport';
if(!is_array($this->stepResult)):
?>
<h2>Изображения товаров</h2>
<? $this->renderPartial('_images_form'); ?>
<? $this->renderPartial('_images_list'); ?>
<?
endif;
?>
