<?php
$banners=\crud\models\ar\Banner::model()->byPriority(6)->published()->findAll();
if(!empty($banners)):
?>
<div class="commercial">
	<div class="commercial__header">
		<h2 class="commercial__title">Реклама</h2>
		<div class="commercial__header-bg"></div>
	</div>
	<div class="commercial__row row">
		<?php foreach($banners as $banner): $banner->incShows(); ?>
			<div class="commercial__col col-lg-6 col-xl-4"><?= \CHtml::link(
    		    $banner->imageBehavior->img(440, 440, true, ['class'=>'commercial__image']), 
    		    $banner->url?:'#!',
    		    ['class'=>'commercial__image-link']
    		); ?>
    		</div>
		<?php endforeach; ?>
	</div>
</div>
<?php 
endif; 
?>