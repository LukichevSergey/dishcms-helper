<?php
/** @var \accounts\controllers\RegController $this */
/** @var \crud\models\ar\accounts\models\Account $account */

?>
<div class="signup account account-page">
	<div class="account-welcome"><?= $this->pageTitle; ?></div>
	<br/>
	<center>
		<p>На указанную Вами почту <b><?= $account->email; ?></b> выслано письмо с ссылкой активации Вашего доступа в личный кабинет.</p>	
	</center>

    <br/>
    <p><a href="/" class="account-link">Перейти на главную страницу</a></p>
</div>