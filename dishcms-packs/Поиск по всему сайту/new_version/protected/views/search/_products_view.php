<?php 
$this->widget('zii.widgets.CListView', array(
	'id'=>'productListView',
    'dataProvider'=>$dataProvider,
    'itemView'=>'/shop/_products',   
    'sorterHeader'=>'Сортировка:',
    'itemsTagName'=>'ul',
    'emptyText' => 'В товаров для отображения.',
    'itemsCssClass'=>'product-list',
    'sortableAttributes'=>array(
        'title',
        'price',
    ),
    'template'=>'{sorter}{items}{pager}',
	'pagerCssClass' => 'pager search-pager',
	'pager' => array(
		'header'=>'Страницы: ',
		'nextPageLabel'=>'&gt;',
		'prevPageLabel'=>'&lt;',
		'cssFile'=>false,
		'htmlOptions'=>array('class'=>'news-pager')
	)
));
?>	