<?php
/** @var \feedback\controllers\AjaxController $this */
/** @var \feedback\components\FeedbackFactory $factory */
/** @var \feedback\models\FeedbackModel $model */
?>
<h1>Новая заявка с сайта <a href="http://<?php echo \Yii::app()->request->serverName; ?>" target="_blank"><?php \ModuleHelper::getParam('sitename'); ?></a></h1>
<h2><?php echo $factory->getTitle(); ?></h2>

<ul>
<?php $model = $factory->getModelFactory()->getModel(); ?>
<?php foreach($factory->getModelFactory()->getAttributes() as $name=>$typeFactory):?>
	<li><b><?php echo $model->getAttributeLabel($name); ?>:</b> <?php echo $typeFactory->getModel()->format($model->$name); ?></li>
<?php endforeach; ?> 
</ul>