<?php
/** @var \components\base\BaseSectionsController $this */
/** @var array[\common\components\base\ActiveRecord] $sections */
?>
<h1><?= $this->getHomeTitle(); ?></h1>

<? $this->renderPartial($this->_sectionsView, compact('sections')); ?>
