<?php
/** @var \menu\widgets\breadcrumbs\simpleBreadcrumsWidget $this */
/** @var array $breadcrumbs */
?>
<style>
ul.breadcrumbs-classic {
	margin: 10px 0 10px 0;
	padding: 0 !important;
}
ul.breadcrumbs-classic li {
	list-style: none;
	display: inline;
	margin: 2px;
} 
</style>
<ul class="breadcrumbs-classic">
	<li><?php echo CHtml::link('Главная', '/'); ?></li>
	<?php foreach($breadcrumbs as $item):?>
		<li>/</li>
		<li><?php echo CHtml::link($item['title'], $item['url']); ?></li>
	<?php endforeach; ?>
</ul>