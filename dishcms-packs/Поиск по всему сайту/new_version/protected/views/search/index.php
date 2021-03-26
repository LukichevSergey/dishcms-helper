<?php $founded=false; ?>
<h1>Результаты поиска</h1>

<?php if($founded|=(bool)$categoryDataProvider->getTotalItemCount()): ?>
	<h2>Разделы каталога</h2>
	<br/>
	<?$this->renderPartial('_category_view', ['dataProvider'=>$categoryDataProvider])?>
<?php endif; ?>

<?php if($founded|=(bool)$data_p->getTotalItemCount()): ?>
	<h2>Продукция</h2>
	<br />
	<div id="product-list-module">
		<?$this->renderPartial('_products_view', ['dataProvider'=>$data_p])?>
	</div>
<?php endif; ?>

<?php if($founded|=(bool)$eventsDataProvider->getTotalItemCount()): ?>
	<h2>Новости</h2>
	<br />
	<?$this->renderPartial('_events_view', ['dataProvider'=>$eventsDataProvider])?>
<?php endif; ?>

<?php if($founded|=(bool)$pagesDataProvider->getTotalItemCount()): ?>
	<h2>Страницы</h2>
	<br />
	<?$this->renderPartial('_page_view', ['dataProvider'=>$pagesDataProvider])?>
<?php endif; ?>

<?php if(!$founded):?>
	<br /><i>Не найдено</i>
<?php endif;?>