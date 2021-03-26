<?php
use common\components\helpers\HYii as Y;
use common\components\helpers\HTools;
use accounts\components\helpers\HAccount;
?>
<div class="marketplace-table-row">
	<div class="marketplace-num">
		<div class="marketplace-title">№</div>
		<div class="marketplace-value"><?= $data->id; ?></div>
	</div>
	<div class="marketplace-adverts">
		<div class="marketplace-title">Adverts</div>
		<div class="marketplace-value">
			<div class="marketplace-partNumber">
				<span class="marketplace-adverts-label">Looking for Part Number: </span>
				<span class="marketplace-adverts-value"><?= $data->part_number; ?></span>
			</div>
			<div class="marketplace-partType">
				<span class="marketplace-adverts-label">NLG tyres / Quantity: </span>
				<span class="marketplace-adverts-value"><?= $data->quantity; ?></span>
			</div>
			<div class="marketplace-condition">
				<span class="marketplace-adverts-label">Condition / Capability Code: </span>
				<span class="marketplace-adverts-value"><?= $data->code; ?></span>
			</div>
		</div>
	</div>
	<div class="marketplace-aircraft">
		<div class="marketplace-title"><?= $data->getDetailTypeLabel(); ?></div>
		<div class="marketplace-value"><?= $data->detail_type_value; ?></div>
	</div>
	<div class="marketplace-category">
		<div class="marketplace-title">Category</div>
		<div class="marketplace-value"><?= $data->category; ?></div>
	</div>
	<div class="marketplace-posted">
		<div class="marketplace-title">Posted on</div>
		<div class="marketplace-value"><? 
		  $time=HTools::isDateEmpty($data->published_date) ? (HTools::isDateEmpty($data->update_time) ? $data->create_time : $data->update_time) : $data->published_date;
		  if(!HTools::isDateEmpty($time)) {
		      echo date_create_from_format("Y-m-d H:i:s", $time)->format("l d/m/Y g:i A");
		  }
		  else {
		      echo '&nbsp;';
		  }
	   ?></div>
	</div>
	<div class="marketplace-list">
		<div class="marketplace-title">Part list</div>
		<div class="marketplace-value">
			<?php if($data->fileBehavior->exists()): ?>
			<a href="javascript:;" class="js-advert-doc-file" data-item="<?=$data->id?>">
				<i class="marketplace-list-icon">
					<img src="/images/xls.svg">
				</i>
				<span>Click here</span>
			</a>
			<?php endif; ?>
		</div>
	</div>
	<div class="marketplace-respond">
		<div class="marketplace-title"></div>
		<?php if($data->account_id != HAccount::account()->id): ?>
			<?php if($data->hasResponse()): ?>
			<div class="marketplace-value">
    			<a href="javascript:;" class="btn btn-advert-respond-sended" data-item="<?=$data->id?>">Sended</a>
    		</div>
			<?php else: ?>
    		<div class="marketplace-value">
    			<a href="#" class="btn js-advert-respond-btn" data-item="<?=$data->id?>">Respond</a>
    		</div>
    		<?php endif; ?>
		<?php else: ?>
			&nbsp;
		<?php endif; ?>
	</div>
</div>