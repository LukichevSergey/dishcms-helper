<?php $this->widget('zii.widgets.CListView', array(
		'id'=>'categoryListView',
		'dataProvider'=>$dataProvider,
		'itemView'=>'_category_item',
		'itemsTagName'=>'ol',
		'pagerCssClass' => 'pager search-pager',
		'template'=>'{items}{pager}',
		'pager' => array(
			'header'=>'Страницы: ',
			'nextPageLabel'=>'&gt;',
			'prevPageLabel'=>'&lt;',
			'cssFile'=>false,
			'htmlOptions'=>array('class'=>'news-pager')
		)
)); ?>