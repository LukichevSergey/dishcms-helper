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
<?php if($data_p->getTotalItemCount()): ?>
<br />
	<?php 
	$products = $data_p->getData(); 
	$pages = $data_p->getPagination();
	?>
	
	<div id="product-list-module">

	<?php 
		$this->widget('zii.widgets.CListView', array(
		    'dataProvider'=>$data_p,
		    'itemView'=>'/shop/_products',   // refers to the partial view named '_post'
		    'sorterHeader'=>'Сортировка:',
		    #'sorterCssClass'=>'filter-menu sort',
		    
		    
		    'itemsTagName'=>'ul',
		    #'ajaxUrl'=>'/shop/category',
		    'emptyText' => 'В товаров для отображения.',
		    'itemsCssClass'=>'product-list',
		    'sortableAttributes'=>array(
		        'title',
		        'price',
		    ),
		    'id'=>'ajaxListView',
		    'template'=>'{sorter}{items}{pager}',
		    
		));
	?>	
	</div>
<?php else: echo '<br /><i>Не найдено</i>'; endif; ?>