<h1>Результаты поиска</h1>

<h2>Новости</h2>
<?php if($eventsDataProvider->getTotalItemCount()): ?>
	<?php $this->widget('zii.widgets.CListView', array(
		'dataProvider'=>$eventsDataProvider,
		'itemView'=>'event_item',
		'itemsTagName'=>'ol',
		'pagerCssClass' => 'pager search-pager',
		'pager' => array(
			'header'=>'Страницы: ',
			'nextPageLabel'=>'&gt;',
			'prevPageLabel'=>'&lt;',
			'cssFile'=>false,
			'htmlOptions'=>array('class'=>'news-pager')
		)
	)); ?>
<?php else: echo '<br /><i>Не найдено</i><br /><br />'; endif; ?>

<h2>Страницы</h2>
<?php if($pagesDataProvider->getTotalItemCount()): ?>
	<?php $this->widget('zii.widgets.CListView', array(
		'dataProvider'=>$pagesDataProvider,
		'itemView'=>'page_item',
		'itemsTagName'=>'ol',
		'pagerCssClass' => 'pager search-pager',
		'pager' => array(
	        'header'=>'Страницы: ',
	        'nextPageLabel'=>'&gt;',
	        'prevPageLabel'=>'&lt;',
	        'cssFile'=>false,
	        'htmlOptions'=>array('class'=>'news-pager')
	    )
	)); ?>
<?php else: echo '<br /><i>Не найдено</i><br /><br />'; endif; ?>
<h2>Продукция</h2>
<?php if($dataProvider->getTotalItemCount()): ?>
<br />
	<?php 
	$products = $dataProvider->getData(); 
	$pages = $dataProvider->getPagination();
	?>
	
	<div id="product-list-module">
	    <?php $this->renderPartial('/shop/_products', compact('products', 'pages')); ?>
	</div>
<?php else: echo '<br /><i>Не найдено</i>'; endif; ?>