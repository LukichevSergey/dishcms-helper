<?php
?>
<div class="inner-page-head">
	<div class="container">
		<div class="inner-page-head-inner">
			<div class="inner-page-head-left">
				<h1>Airparts marketplace</h1>
				<h3>Add your buy or sell message</h3>
				<div class="inner-page-head-buttons">
					<a href="/accounts/account/addPartsWantedAdvert" class="btn">Parts Wanted</a>
					<a href="/accounts/account/addSaleAdvert" class="btn">Parts For Sale</a>
				</div>
			</div>
			<div class="inner-page-head-right">
				<div class="inner-page-head-img">
					<img src="/images/am.png">
				</div>
			</div>
		</div>
	</div>
</div>

<?php $this->renderPartial('_advert_list', compact('type', 'dataProvider')); ?>