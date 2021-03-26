<?php
/** @var \accounts\controllers\AccountController $this */
/** @var \crud\models\ar\accounts\models\Account $account */
use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HTools;
use accounts\components\helpers\HAccount;
use crud\models\ar\accounts\models\Coupon;

Y::jsCore('jquery.ui');
Y::jsCore('maskedinput');
Y::module('common')->publishJs('js/tools/activeform.js');
//Y::module('accounts')->publishJs('controllers/account/scripts.js');
//Y::module('accounts')->publishLess('controllers/account/styles.less');

$accountBankInforation=$account->getBankInformation();
?>

	
<div class="account-main account-info">
	<div class="account-info-head">
		<h2>Personal information</h2>
		<a href="/accounts/account/edit" class="btn">Edit information</a>
	</div>
	<div class="account-info-body">
		<div class="account-info-body-row">
			<div class="account-info-body-left">
				<ul>
					<li>
						<div class="account-info-name">Company name</div>
						<div class="account-info-value"><?= $account->company; ?></div>
					</li>
					<li>
						<div class="account-info-name">Category</div>
						<div class="account-info-value"><?= $account->getCategoryLabel(); ?></div>
					</li>
					<li>
						<div class="account-info-name">Country</div>
						<div class="account-info-value"><?= $account->country->title; ?></div>
					</li>
					<li>
						<div class="account-info-name">Contact personal</div>
						<div class="account-info-value"><?= $account->name; ?></div>
					</li>
					<li>
						<div class="account-info-name">Phone number</div>
						<div class="account-info-value"><?= $account->formatPhone(); ?></div>
					</li>
					<li>
						<div class="account-info-name">E-mail</div>
						<div class="account-info-value"><?= $account->email; ?></div>
					</li>
				</ul>
				<?php /* ?><p class="account-info-tip">Please fill in all fields marked with (*)</p><? /**/ ?>
			</div>

			<div class="account-info-body-right">
				<div class="account-info-logo">
					<?php if($account->companyLogoBehavior->exists()) { ?>
						<?= $account->companyLogoBehavior->img(106, 142); ?>
					<?php } else { ?>
						<img src="/images/corp-logo.svg">
					<?php } ?>
				</div>
			</div>
		</div>
	</div>

	<div class="account-bank">
		<div class="account-bank-head">
			<h3>Bank information</h3>
			<a href="/accounts/account/editBankInformation">Edit information</a>
		</div>
		<?php if(!empty($accountBankInforation)): ?>
		<div class="account-bank-list">
			<ul>
				<?php foreach($accountBankInforation as $item): ?>
				<li>
					<div class="account-bank-name"><?= $item['title']; ?></div>
					<div class="account-bank-value"><?= $item['value']; ?></div>
				</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php endif; ?>
	</div>
</div>