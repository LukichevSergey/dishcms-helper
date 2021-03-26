<?php
/** @var BreadcrumbsArrayWidget $this */
/** @var array $breadcrumbs */

if($this->importStyles) include('_styles.php');
?>
<ul class="breadcrumbs">
	<li><?php echo CHtml::link($this->homeTitle, '/'); ?></li>
	<?php foreach($breadcrumbs as $item):?>
		<li>/</li>
		<li><?php echo CHtml::link($item['title'], $item['url']); ?></li>
	<?php endforeach; ?>
</ul>