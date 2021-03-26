<?php
/** @var \menu\widgets\breadcrumbs\simpleBreadcrumsWidget $this */
/** @var array $breadcrumbs */

use \menu\components\helpers\UrlHelper;
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
<ul <?php if($this->cssClass) echo "class=\"{$this->cssClass}\""; ?>>
	<li><?php echo CHtml::link('Главная', '/'); ?></li>
	<?php foreach($breadcrumbs as $item):?>
		<li>/</li>
		<li><?php echo CHtml::link($item->title, UrlHelper::createUrl($item, $this->adminMode)); ?></li>
	<?php endforeach; ?>
</ul>