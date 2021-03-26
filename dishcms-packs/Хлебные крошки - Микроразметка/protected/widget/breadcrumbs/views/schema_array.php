<?php
/** @var BreadcrumbsArrayWidget $this */
/** @var array $breadcrumbs */

if($this->importStyles) include('_styles.php');
?>
<div class="breadcrumbs">
	<div itemscope="" itemtype="http://data-vocabulary.org/Breadcrumb">
		<a href="<?=Yii::app()->createUrl('site/index') ?>" itemprop="url"><span itemprop="title"><?=$this->homeTitle?></span></a>
	</div>
	<?php foreach($breadcrumbs as $item):?>
		<div>/</div>
		<div itemscope="" itemtype="http://data-vocabulary.org/Breadcrumb">
			<?php if($item === end($breadcrumbs)):?>
				<span itemprop="title"><?= $item['title'] ?></span>
			<?php else:?>
				<a href="<?= $item['url'] ?>" itemprop="url"><span itemprop="title"><?= $item['title'] ?></span></a>
			<?php endif;?>
		</div>
	<?php endforeach; ?>
</div>
