<?php
/** @var \components\base\BaseSectionsController $this */
/** @var \common\components\base\ActiveRecord $model */
?>
<h1><?= $model->getSeoH1(); ?></h1>

<?= $model->text; ?>

<?= \CHtml::link('Назад', $model->section->getUrl(), ['style'=>'margin-top:20px']); ?>
