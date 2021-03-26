<? 
/** @var \ShopController $this */
/** @var \Rangeof $model */
/** @var \CActiveDataProvider[Product] $dataProvider */
use extend\modules\seo\components\helpers\HSeo; 

?>
<?= HSeo::h1(); ?>

<div class="shop-filter-page">
	<div class="row">
		<div class="col-sm-12 col-lg-12">
			<? if($filterData=HFilter::rangeof($model)): ?>
			<div class="shop-filter-block">
				<div class="filter-header">
					<p>Выбор по параметрам</p>
				</div>
				<div class="row">
					<? $this->widget('widget.filters.ProductFilter', ['data'=>$filterData]); ?>		
				</div>				
			</div>
			<? endif; ?>
			<div class="shop-price-block">
				<?= $model->preview_text; ?>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-12 col-lg-12">
			<? $this->widget('\extend\modules\pages\widgets\ListWidget', [
				'cid'=>'rangeof', 
				'sort'=>'rangeof', 
				'view'=>'theme.rangeof.rangeof_list',
				'options'=>['pagination'=>['pageSize'=>9999999]]
			]); ?>
			<div id="product-list-module">
				<? $this->renderPartial('_products_listview', compact('dataProvider')); ?>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-12 col-lg-12">
			<div class="page__detail-text"><?= $model->detail_text; ?></div>
		</div>
	</div>
</div>

