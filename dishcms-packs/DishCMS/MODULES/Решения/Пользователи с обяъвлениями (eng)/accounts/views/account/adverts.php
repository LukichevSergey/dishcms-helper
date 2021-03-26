<?php
/** @var \CActiveDataProvider[\crud\models\ar\accounts\models\Advert] $dataProvider */
use crud\models\ar\accounts\models\Advert;
?>
<div class="account-main account-myAdverts">
	<div class="account-main-head">
		<h2>My adverts</h2>
	</div>
	<div class="account-adverts-table">
		<div class="account-adverts-head">
			<div class="account-adverts-num">№</div>
			<div class="account-adverts-adverts">Adverts</div>
			<div class="account-adverts-aircraft"><?= implode(' / ', Advert::model()->getAdvertDetailTypeList(true))?></div>
			<div class="account-adverts-category">Category</div>
		</div>
		<?php 
		$this->widget('zii.widgets.CListView', [
            'dataProvider'=>$dataProvider,
            'itemView'=>'_advert_item',   
		    'itemsCssClass'=>'account-adverts-body',
		    'template'=>'{items}<div class="marketplace-pager">{pager}</div>',
		    'pagerCssClass'=>'pagination',
		    'pager'=>[
		        'lastPageLabel'=>false,
		        'firstPageLabel'=>false,
		        'prevPageLabel'=>false,
		        'nextPageLabel'=>'next',
		        'header'=>''
		    ],
		    'emptyText'=>'No ads found'
        ]);
		?>
	</div>
</div>