<?php
/** @var \components\base\BaseSectionsController $this */
/** @var \CActiveDataProvider[\common\components\base\ActiveRecord] $itemsDataProvider */

if($itemsDataProvider->getTotalItemCount() > 0): 
?>
<div class="events_page">
  <?php foreach($itemsDataProvider->getData() as $data): ?>
  <div class="event">
	  <? /* ?><p class="created"><?= $data->getDate(); ?></p><? /**/ ?>
	  <h2><?= CHtml::link($data->title, $data->getUrl()); ?></h2>
	  <div class="event_img"><?= CHtml::link($data->img(200, 160, true), $data->getUrl()); ?></div>
	  <div class="intro"><p><?= $data->preview_text; ?></p></div>
	  <div class="clearfix"></div>
	  <?= CHtml::link('Подробнее &rarr;', $data->getUrl(), ['class'=>'btn']); ?>
  </div>
  <?php endforeach; ?>
</div>

<?php 
if($itemsDataProvider->getPagination()->getPageCount() > 0) { 
	$this->widget('\DLinkPager', [
		'header'=>'Страницы: ',
		'pages'=>$itemsDataProvider->getPagination(),
		'nextPageLabel'=>'&gt;',
		'prevPageLabel'=>'&lt;',
		'cssFile'=>false,
		'htmlOptions'=>['class'=>'news-pager']
	]); 
} 
?>

<? endif; ?>
