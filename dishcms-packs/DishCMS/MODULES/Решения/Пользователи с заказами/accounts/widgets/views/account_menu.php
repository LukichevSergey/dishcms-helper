<?php
use common\components\helpers\HYii as Y;

?>
<nav id="account-menu">
	<ul>
		<li<?php if(Y::isAction(Y::controller(), 'account', ['index', 'edit', 'editBankInformation'])) { echo ' class="active"'; }?>><a href="/accounts/account">
			<span class="account-side-nav-title">Personal information</span>
		</a></li>
		<li<?php if(Y::isAction(Y::controller(), 'account', ['addPartsWantedAdvert'])) { echo ' class="active"'; }?>><a href="/accounts/account/addPartsWantedAdvert">
			<span class="account-side-nav-title">Add advert</span>
			<span class="account-side-nav-subtitle">Parts Wanted</span>
		</a></li>
		<li<?php if(Y::isAction(Y::controller(), 'account', ['addSaleAdvert'])) { echo ' class="active"'; }?>><a href="/accounts/account/addSaleAdvert">
			<span class="account-side-nav-title">Add advert</span>
			<span class="account-side-nav-subtitle">For Sale</span>
		</a></li>
		<li<?php if(Y::isAction(Y::controller(), 'account', ['adverts'])) { echo ' class="active"'; }?>><a href="/accounts/account/adverts">
			<span class="account-side-nav-title">My adverts</span>
		</a></li>
		<li><a href="/accounts/auth/logout">
			<span class="account-side-nav-title">Exit</span>
		</a></li>
	</ul>
</nav>