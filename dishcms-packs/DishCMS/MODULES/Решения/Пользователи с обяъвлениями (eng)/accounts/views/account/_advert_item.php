<?php
/** @var \crud\models\ar\accounts\models\Advert $data */
use crud\models\ar\accounts\models\Advert;

$allowDetailTypes=Advert::model()->getAdvertDetailTypeList(true);
?>
<div class="account-adverts-row <?= ($data->type == Advert::TYPE_SALE) ? 'account-adverts-row-sale' : 'account-adverts-row-wanted-parts'; ?>">
	<div class="account-adverts-row-top">
		<div class="account-adverts-num">
			<div class="marketplace-title">№</div>
			<div class="marketplace-value"><?= $data->id; ?></div>
		</div>
		<div class="account-adverts-adverts">
			<div class="marketplace-title">Adverts</div>
			<div class="marketplace-value">
				<div class="account-adverts-partNumber">
					<span class="account-adverts-adverts-label">Looking for Part Number: </span>
					<span class="account-adverts-adverts-value"><?= $data->part_number; ?></span>
				</div>
				<div class="account-adverts-partType">
					<span class="account-adverts-adverts-label"><?= $data->part_type; ?> / Quantity: </span>
					<span class="account-adverts-adverts-value"><?= $data->quantity; ?></span>
				</div>
				<div class="account-adverts-condition">
					<span class="account-adverts-adverts-label">Condition / Capability Code: </span>
					<span class="account-adverts-adverts-value"><?= $data->code; ?></span>
				</div>
			</div>
		</div>
		<div class="account-adverts-aircraft">
			<div class="marketplace-title"><?= $data->getDetailTypeLabel(); ?></div>
			<div class="marketplace-value">
				<?php if(count($allowDetailTypes) > 1) echo $data->getDetailTypeLabel() . ':<br/>'; ?>
				<?= $data->detail_type_value; ?>
			</div>
		</div>
		<div class="account-adverts-category">
			<div class="marketplace-title">Category</div>
			<div class="marketplace-value"><?= $data->category; ?></div>
		</div>
	</div>
	<div class="account-adverts-row-bottom">
		<div class="account-adverts-status">
			<div class="account-adverts-status-title">Status:</div>
			<?php if($data->published): ?>
				<div class="account-adverts-status-label activity">Activity</div>
				<div class="account-adverts-status-date"><?= date_create_from_format('Y-m-d H:i:s', $data->published_date)->format('l d/m/Y g:i A'); ?></div>
			<?php else: ?>
    			<div class="account-adverts-status-label moderation">On moderation</div>
    			<div class="account-adverts-status-date"></div>
			<?php endif; ?>
		</div>
		<div class="account-adverts-doc">
			<div class="doc-block">
				<?php if($data->fileBehavior->exists()): ?>
				<a href="<?=$data->getAdvertFileNameDownloadUrl()?>" for="upload-doc">
					<i class="doc-icon">
						<img src="/images/xls.svg">
					</i>
					<span>Document</span>
				</a>
				<?php else: ?>
				&nbsp;
				<?php endif; ?>
			</div>
		</div>
		<div class="account-adverts-edit">
			<a class="btn" href="/accounts/account/advertEdit/<?=$data->id?>">Edit</a>
		</div>
	</div>
</div>