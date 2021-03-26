<h1><?=D::cms('events_title', Yii::t('events','events_title'))?></h1>

<div class="events_page">
  <?php foreach($events as $event): ?>
  <div class="event">
      <p class="created"><?php echo $event->date; ?></p>
      <h2><?php echo CHtml::link($event->title, array('site/event', 'id'=>$event->id)); ?></h2>
      <? if($event->previewImg): ?>
        <div class="event_img">
          <a href="<?= Yii::app()->createUrl('site/event', array('id'=>$event->id)); ?>">
            <img src="<?=$event->previewImg?>" alt="<?php echo $event->title; ?>" title="<?php echo $event->title; ?>">
          </a>
        </div>
      <? endif; ?>
      <div class="intro"><p><?php echo $event->intro; ?></p></div>
      <div class="clearfix"></div>
      <?php echo CHtml::link('Подробнее &rarr;', array('site/event', 'id'=>$event->id), array('class'=>'more-info')); ?>
  </div>
  <?php endforeach; ?>
</div>

<?php $this->widget('DLinkPager', array(
  'header'=>'Страницы: ',
  'pages'=>$pages,
  'nextPageLabel'=>'&gt;',
  'prevPageLabel'=>'&lt;',
  'cssFile'=>false,
  'htmlOptions'=>array('class'=>'news-pager')
)); ?>
      

